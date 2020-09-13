(function ($) {
    //
    // Auto Expand Note Inputs
    // ------------------------------------------------------------
    function setNoteHeight(textAreaSelected, getHeightOfText) {
        setTimeout(function () {
            var formHeight = getHeightOfText.height() + 80;

            // Update the height of the .tox tox-tinymce class text area
            if (formHeight > 200) {
                $(textAreaSelected).css('height', formHeight);
            }

        }, 500);
    }

    //
    // Auto Expand Note Inputs - On click
    // ------------------------------------------------------------
    $('.ldin-add-note').click(function () {
        var textAreaSelected = $(this).next('.ldin-notes-form').find('.tox.tox-tinymce');
        var getHeightOfText = $(this).next('.ldin-notes-form').find('iframe').contents().find('.mce-content-body');

        setNoteHeight(textAreaSelected, getHeightOfText);
    });

    //
    // Auto Expand Note Inputs - On type and paste.
    // ------------------------------------------------------------
    $(".ldin-notes-form textarea").on('change keyup paste', function () {
        var textAreaSelected = $(this).next('.tox.tox-tinymce');
        var getHeightOfText = $(this).next('.tox.tox-tinymce').find('iframe').contents().find('.mce-content-body');

        setNoteHeight(textAreaSelected, getHeightOfText);
    });
    
    //
    // Auto Expand Note Inputs - On window resize
    // ------------------------------------------------------------
    $(window).on('resize', function () {
        $(".ldin-notes-form textarea").each(function () {
            var textAreaSelected = $(this).next('.tox.tox-tinymce');
            var getHeightOfText = $(this).next('.tox.tox-tinymce').find('iframe').contents().find('.mce-content-body');
            setNoteHeight(textAreaSelected, getHeightOfText);
        })
    });

    //
    // Reset note area height on save.
    // ------------------------------------------------------------
    $('.ldin-save-notes').click(function () {
        $(this).parent().prev().css("height", "200px");
    });

})(jQuery);
