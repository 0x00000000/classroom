function ActiveLesson(isTeacher, params) {

function Command(data) {
    this.data = data;
    this.type = 'Command';
}

Command.prototype.execute = function() {
    console.log('Base command execute data', this.data);
}

Command.prototype.getData = function() {
    return this.data;
}

Command.prototype.getType = function() {
    return this.type;
}

Command.prototype.serialize = function() {
    var data = {
        type: this.type,
        data: this.data
    };
    
    return JSON.stringify(data);
}

function ScrollCommand(data) {
    Command.apply(this, arguments);
    this.type = 'ScrollCommand';
}

ScrollCommand.prototype = Object.create(Command.prototype);
ScrollCommand.prototype.constructor = ScrollCommand;

ScrollCommand.prototype.execute = function() {
    console.log('Scroll command execute data', this.data);
    if (this.data != null) {
        window.scrollTo({
            top: this.data,
            left: 0,
            behavior: 'smooth',
        });
    }
}

function ClickCommand(data) {
    Command.apply(this, arguments);
    this.type = 'ClickCommand';
    this.pointerClassName = 'pointer';
}

ClickCommand.prototype = Object.create(Command.prototype);
ClickCommand.prototype.constructor = ClickCommand;

ClickCommand.prototype.execute = function() {
    console.log('Click command execute data', this.data);
    if (this.data && typeof this.data.x !== 'undefined'  && typeof this.data.y !== 'undefined') {
        var pointer = document.createElement('div');
        pointer.className = this.pointerClassName;
        pointer.style.left = this.data.x + 'px';
        pointer.style.top = this.data.y + 'px';
        document.body.appendChild(pointer);
        setTimeout(function(){pointer.parentNode.removeChild(pointer);}, 5000);
        document.documentElement.scrollTop = this.data;
    }
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

function Controller(params) {
    if (typeof params === 'undefined') {
        params = {};
    }
    
    this.innerCommands = [];
    this.outerCommands = [];
    this.params = params;
}

Controller.prototype.getCommand = function(type) {
    var result = null;
    
    if (typeof this.innerCommands[type] !== 'undefined') {
        result = this.innerCommands[type]; 
        delete this.innerCommands[type];
    }
    
    return result;
}

Controller.prototype.setCommand = function(type, command) {
    if (type) {
        this.outerCommands[type] = command;
    }
}

Controller.prototype.executeCommands = function() {
    for (var type in this.outerCommands) {
        if (this.outerCommands[type] instanceof Command) {
            this.outerCommands[type].execute();
            delete this.outerCommands[type];
        }
    }
}

Controller.prototype.setLock = function() {
    // Do nothing set lock by default.
}

function UiController(params) {
    var self = this;
    
    function checkLock(event) {
        currentTime = (new Date()).getTime();
        if (currentTime < self.unlockTime) {
            event.stopPropagation();
            event.preventDefault();
            event.stopImmediatePropagation();
        }
    }
    
    if (typeof params === 'undefined') {
        params = {};
    }
    
    Controller.apply(this, arguments);
    
    this.unlockTime = 0;
    this.lockTimePeriod = 5000;
    
    this.prevData = [];
    this.prevData['ScrollCommand'] = this.getUiData('ScrollCommand');
    
    $(window).on('scroll keypress keyup keydown click dbclick mousedown mouseup mousemove mousewheel wheel DOMMouseScroll MozMousePixelScroll', function(event) {
        checkLock(event);
    });
    
    $(document).on('click', function(event) {
        if (event && event.originalEvent) {
            var data = {x: event.originalEvent.x, y: event.originalEvent.y};
            var command = new ClickCommand(data);
            command.execute();
            self.innerCommands['ClickCommand'] = command;
        }
    });
}

UiController.prototype = Object.create(Controller.prototype);
UiController.prototype.constructor = UiController;

UiController.prototype.getCommand = function(type) {
    var command = null;
    
    switch (type) {
        case 'ScrollCommand':
            var data = this.getUiData(type);
            if (data !== this.prevData[type]) {
                command = new ScrollCommand(data);
                this.prevData[type] = data;
            }
            break;
        case 'ClickCommand':
            if (typeof this.innerCommands[type] !== 'undefined') {
                command = this.innerCommands[type];
                delete this.innerCommands[type];
            }
            break;
        default:
            break;
    }
    
    return command;
}

UiController.prototype.executeCommands = function() {
    for (var type in this.outerCommands) {
        if (this.outerCommands[type] instanceof Command) {
            this.outerCommands[type].execute();
            if (type === 'ScrollCommand') {
                this.prevData[type] = this.outerCommands[type].getData();
            }
            delete this.outerCommands[type];
        }
    }
}

UiController.prototype.setLock = function() {
    this.unlockTime = new Date().getTime() + this.lockTimePeriod;
}

UiController.prototype.getUiData = function(type) {
    var result = null;
    
    switch (type) {
        case 'ScrollCommand':
            result = window.pageYOffset || document.documentElement.scrollTop;
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
    
    if (! this.url) {
        console.log('Url is empty.');
        return;
    }
    
    requestData = {command: []};
    for (var type in this.outerCommands) {
        if (this.outerCommands[type] instanceof Command) {
            requestData['command'].push(this.outerCommands[type].serialize());
        }
    }
    
    this.outerCommands = {};
    
    $.ajax(
        self.url,
        {
            dataType: 'json',
            method: 'POST',
            data: requestData,
            success: function(data) {
                if (typeof data['command'] === 'object' && data['command'].length) {
                    for (var i = 0; i < data['command'].length; i++) {
                        var command = CommandFactory.create(data['command'][i]);
                        self.innerCommands[command.getType()] = command;
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
        var uiCommand = uiController.getCommand(types[i]);
        if (uiCommand) {
            webController.setCommand(types[i], uiCommand);
        } else {
            var webCommand = webController.getCommand(types[i]);
            if (webCommand) {
                uiController.setCommand(types[i], webCommand);
            }
        }
    }
    
    var types = ['ClickCommand'];
    for (var i = 0; i < types.length; i++) {
        var uiCommand = uiController.getCommand(types[i]);
        var webCommand = webController.getCommand(types[i]);
        uiController.setCommand(types[i], webCommand);
        webController.setCommand(types[i], uiCommand);
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
        var webCommand = webController.getCommand(types[i]);
        if (webCommand) {
            uiController.setCommand(types[i], webCommand);
            uiController.setLock();
        } else {
            var uiCommand = uiController.getCommand(types[i]);
            if (uiCommand) {
                webController.setCommand(types[i], uiCommand);
            }
        }
    }
    
    var types = ['ClickCommand'];
    for (var i = 0; i < types.length; i++) {
        var uiCommand = uiController.getCommand(types[i]);
        var webCommand = webController.getCommand(types[i]);
        uiController.setCommand(types[i], webCommand);
        webController.setCommand(types[i], uiCommand);
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
        self = this;
        this.strategy.act(this.uiController, this.webController);
        setTimeout(function() {self.act()}, 1000);
    }
}

if (params && typeof params['requestUrl'] !== 'undefined') {
    var application = new Application(isTeacher, params['requestUrl']);
    application.start();
}

}
