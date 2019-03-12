function ActiveLesson(isTeacher, params) {

var whiteBoard;
var application;

$(document).ready(function() {
    if (! params)
        return;
    
    if (typeof params['requestUrl'] === 'undefined')
        return;
    
    whiteBoard = $('.whiteBoard').get(0);
    
    if (! whiteBoard)
        return;
    
    application = new Application(isTeacher, params['requestUrl']);
    application.start();
});

function Command(data) {
    this._data = data;
    this._type = 'Command';
    this._isSingle = true;
}

Command.prototype.execute = function() {
    console.log('Base command execute data', this.getData());
}

Command.prototype.getData = function() {
    return this._data;
}

Command.prototype.getType = function() {
    return this._type;
}

Command.prototype.serialize = function() {
    var data = {
        type: this._type,
        data: this._data
    };
    
    return JSON.stringify(data);
}

Command.prototype.isSingle = function() {
    return this._isSingle;
}

function CommandFactory() {
}

CommandFactory.create = function(serialized) {
    var command = null;
    
    if (typeof serialized === 'string') {
        try {
            var data = JSON.parse(serialized);
            if (
                data
                && typeof data['type'] !== 'undefined'
                && typeof data['data'] !== 'undefined'
            ) {
                switch (data['type']) {
                    case 'ScrollCommand':
                        command = new ScrollCommand(data['data']);
                        break;
                    case 'ClickCommand':
                        command = new ClickCommand(data['data']);
                        break;
                    default:
                        break;
                }
            }
        } catch (e) {
            console.log('Unrecognazible command', e);
        }
        
        return command;
    }
}

function ScrollCommand(data) {
    Command.apply(this, arguments);
    this._type = 'ScrollCommand';
}

ScrollCommand.prototype = Object.create(Command.prototype);
ScrollCommand.prototype.constructor = ScrollCommand;

ScrollCommand.prototype.execute = function() {
    console.log('Scroll command execute data', this.getData());
    if (this.getData() != null) {
        whiteBoard.scrollTo({
            top: this.getData(),
            left: 0,
            behavior: 'instant',
        });
    }
}

function ClickCommand(data) {
    Command.apply(this, arguments);
    this._type = 'ClickCommand';
    this._pointerClassName = 'pointer';
    this._isSingle = false;
}

ClickCommand.prototype = Object.create(Command.prototype);
ClickCommand.prototype.constructor = ClickCommand;

ClickCommand.prototype.execute = function() {
    console.log('Click command execute data', this.getData());
    if (this.getData() && typeof this.getData().x !== 'undefined'  && typeof this.getData().y !== 'undefined') {
        var pointer = document.createElement('div');
        pointer.className = this._pointerClassName;
        pointer.style.left = this.getData().x + 'px';
        pointer.style.top = this.getData().y + 'px';
        whiteBoard.appendChild(pointer);
        setTimeout(function() {
            $(pointer).hide('slow', function() {
                pointer.parentNode.removeChild(pointer);
            });
            
        }, 5000);
    }
}

function Controller(params) {
    if (typeof params === 'undefined') {
        params = {};
    }
    
    this.innerCommands = {}; // For inner use.
    this.outerCommands = {}; // This commands will be executed.
    this.params = params;
}

Controller.prototype.popCommands = function(type) {
    var result = null;
    
    if (this.innerCommands[type] instanceof Array) {
        result = this.innerCommands[type]; 
        delete this.innerCommands[type];
    }
    
    return result;
}

Controller.prototype.addCommands = function(type, commands) {
    if (type && commands instanceof Array && commands.length) {
        for (var i = 0; i < commands.length; i++) {
            if (commands[i].getType() !== type)
                continue;
            this.addOuterCommand(commands[i]);
        }
    }
}

Controller.prototype.addInnerCommand = function(command) {
    if (! (command instanceof Command))
        return;
    
    var type = command.getType();
    if (command.isSingle()) {
        this.innerCommands[type] = [command];
    } else {
        if (! (this.innerCommands[type] instanceof Array)) {
            this.innerCommands[type] = [];
        }
        this.innerCommands[type].push(command);
    }
}

Controller.prototype.addOuterCommand = function(command) {
    if (! (command instanceof Command))
        return;
    
    var type = command.getType();
    if (command.isSingle()) {
        this.outerCommands[type] = [command];
    } else {
        if (! (this.outerCommands[type] instanceof Array)) {
            this.outerCommands[type] = [];
        }
        this.outerCommands[type].push(command);
    }
}

Controller.prototype.executeCommands = function() {
    for (var type in this.outerCommands) {
        if (this.outerCommands[type] instanceof Array) {
            for (var i = 0; i < this.outerCommands[type].length; i++) {
                this.outerCommands[type][i].execute();
            }
        }
    }
    this.outerCommands = {};
}

Controller.prototype.setLock = function() {
    // Do nothing set lock by default.
}

function UiController(params) {
    var self = this;
    
    if (typeof params === 'undefined') {
        params = {};
    }
    
    Controller.apply(self, arguments);
    
    function checkLock(event) {
        currentTime = (new Date()).getTime();
        if (currentTime < self.unlockTime) {
            event.stopPropagation();
            event.preventDefault();
            event.stopImmediatePropagation();
        }
    }
    
    self.unlockTime = 0;
    self.lockWaitTime = 5000;
    
    self.prevData = {};
    self.prevData['ScrollCommand'] = self.getUiData('ScrollCommand');
    
    $(window).on('scroll keypress keyup keydown click dbclick mousedown mouseup mousemove mousewheel wheel DOMMouseScroll MozMousePixelScroll', function(event) {
        checkLock(event);
    });
    
    $(whiteBoard).on('click', function(event) {
        if (event && event.originalEvent) {
            var data = {x: event.originalEvent.layerX, y: event.originalEvent.layerY};
            var command = new ClickCommand(data);
            command.execute();
            self.addInnerCommand(command);
        }
    });
}

UiController.prototype = Object.create(Controller.prototype);
UiController.prototype.constructor = UiController;

UiController.prototype.popCommands = function(type) {
    var commands = null;
    
    switch (type) {
        case 'ScrollCommand':
            var data = this.getUiData(type);
            if (data !== this.prevData[type]) {
                commands = [new ScrollCommand(data)];
                this.prevData[type] = data;
            }
            break;
        case 'ClickCommand':
            if (this.innerCommands[type]) {
                commands = this.innerCommands[type];
                delete this.innerCommands[type];
            }
            break;
        default:
            break;
    }
    
    return commands;
}

UiController.prototype.setLock = function() {
    this.unlockTime = new Date().getTime() + this.lockWaitTime;
}

UiController.prototype.getUiData = function(type) {
    var result = null;
    
    switch (type) {
        case 'ScrollCommand':
            result = whiteBoard.scrollTop;
            break;
        default:
            break;
    }
    
    return result;
}

function WebController(params) {
    if (typeof params === 'undefined') {
        params = {};
    }
    
    Controller.apply(this, arguments);
    
    if (typeof params['url'] !== 'undefined') {
        this.url = params['url'];
    } else {
        this.url = null;
    }
}

WebController.prototype = Object.create(Controller.prototype);
WebController.prototype.constructor = WebController;

WebController.prototype.executeCommands = function() {
    var self = this;
    
    if (! self.url) {
        console.log('Url is empty.');
        return;
    }
    
    requestData = {commands: {}};
    for (var type in self.outerCommands) {
        if (self.outerCommands[type] instanceof Array) {
            requestData['commands'][type] = [];
            for (var i = 0; i < self.outerCommands[type].length; i++) {
                requestData['commands'][type].push(self.outerCommands[type][i].serialize());
            }
        }
    }
    self.outerCommands = {};
    
    $.ajax(
        self.url,
        {
            dataType: 'json',
            method: 'POST',
            data: requestData,
            success: function(data) {
                if (data['commands'] instanceof Object) {
                    for (var type in data['commands']) {
                        if (data['commands'][type] instanceof Array) {
                            for (var i = 0; i < data['commands'][type].length; i++) {
                                var command = CommandFactory.create(data['commands'][type][i]);
                                self.addInnerCommand(command);
                            }
                        }
                    }
                }
            },
            error: function() {
            },
        }
    );
}

function Strategy() {
}

Strategy.prototype.act = function(uiController, webController) {
    // Do nothing.
}

function TeacherStrategy() {
    Strategy.apply(this, arguments);
}

TeacherStrategy.prototype = Object.create(Strategy.prototype);
TeacherStrategy.prototype.constructor = TeacherStrategy;

TeacherStrategy.prototype.act = function(uiController, webController) {
    var types = ['ScrollCommand'];
    for (var i = 0; i < types.length; i++) {
        var uiCommands = uiController.popCommands(types[i]);
        if (uiCommands) {
            webController.addCommands(types[i], uiCommands);
        } else {
            var webCommands = webController.popCommands(types[i]);
            if (webCommands) {
                uiController.addCommands(types[i], webCommands);
            }
        }
    }
    
    var types = ['ClickCommand'];
    for (var i = 0; i < types.length; i++) {
        var uiCommands = uiController.popCommands(types[i]);
        var webCommands = webController.popCommands(types[i]);
        uiController.addCommands(types[i], webCommands);
        webController.addCommands(types[i], uiCommands);
    }
    
    uiController.executeCommands();
    webController.executeCommands();
}

function StudentStrategy() {
    Strategy.apply(this, arguments);
}

StudentStrategy.prototype = Object.create(Strategy.prototype);
StudentStrategy.prototype.constructor = StudentStrategy;

StudentStrategy.prototype.act = function(uiController, webController) {
    var types = ['ScrollCommand'];
    for (var i = 0; i < types.length; i++) {
        var webCommands = webController.popCommands(types[i]);
        if (webCommands) {
            uiController.addCommands(types[i], webCommands);
            uiController.setLock();
        } else {
            var uiCommands = uiController.popCommands(types[i]);
            if (uiCommands) {
                webController.addCommands(types[i], uiCommands);
            }
        }
    }
    
    var types = ['ClickCommand'];
    for (var i = 0; i < types.length; i++) {
        var uiCommands = uiController.popCommands(types[i]);
        var webCommands = webController.popCommands(types[i]);
        uiController.addCommands(types[i], webCommands);
        webController.addCommands(types[i], uiCommands);
    }
    
    uiController.executeCommands();
    webController.executeCommands();
}

function Application(isTeacher, requestUrl) {
    this.isTeacher = isTeacher;
    var uiParams = {};
    this.uiController = new UiController(uiParams);
    var webParams = {url: requestUrl};
    this.webController = new WebController(webParams);
    if (isTeacher) {
        this.strategy = new TeacherStrategy();
    } else {
        this.strategy = new StudentStrategy();
    }
    
    this.active = false;
}


Application.prototype.setStrategy = function(strategy) {
    if (strategy instanceof Strategy) {
        this.Strategy = strategy;
    }
}

Application.prototype.start = function() {
    this.active = true;
    this.act();
}

Application.prototype.stop = function() {
    this.active = false;
}

Application.prototype.act = function() {
    if (this.active) {
        var self = this;
        this.strategy.act(this.uiController, this.webController);
        setTimeout(function() {self.act()}, 1000);
    }
}

}
