function waitForActiveLesson(requestUrl, redirectUrl) {

    function ask() {
        if (! requestUrl || ! redirectUrl)
            return;
        
        $.ajax(
            requestUrl,
            {
                dataType: 'json',
                success: function(data) {
                    if (
                        typeof data.lessonId !== 'undefined'
                        && typeof data.teacherId !== 'undefined'
                        && data.lessonId
                        && data.teacherId
                    ) {
                        window.location.href = redirectUrl + '/' + data.teacherId + '/' + data.lessonId;
                    }
                },
                error: function() {
                },
                complete: function() {
                    setTimeout(ask, 1000);
                },
            }
        );
    }
    
    setTimeout(ask, 1000);
}

function initWhiteBoard() {
    
    function setWhiteBoardHeight() {
        if ($('.activeLessonContent').length) {
            var box = $('.activeLessonContent').get(0).getBoundingClientRect();
            var fromTop = box.top + pageYOffset;
            var height = Math.floor(document.documentElement.clientHeight - fromTop);
            $('.activeLessonContent').height(height + 'px');
        }
    }
    $(document).ready(function() {
        setWhiteBoardHeight();
    });
    $(window).on('resize', function() {
        setWhiteBoardHeight();
    });
    
}