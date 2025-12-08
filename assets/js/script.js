$(document).ready(function ($) {
    
  
    
    function menuHide(){
        $('.menu').addClass('slideOutRight').removeClass('slideInRight');
        $('.bugger').toggleClass('burgger-icon');
    }
    
    $('.menu-nav-left').click(function(){
         menuHide();
    });
    
    $('.bugger').click(function () {
        $('.menu').css('visibility', 'visible');
        $('.menu').toggleClass('slideOutRight slideInRight');
        $(this).toggleClass('burgger-icon');
    });
    
    $('.nav ul li a').click(function(){
         var a = $(this).attr("href");
            var b = $(a).offset();
            
            
            if ($(window).width() < 767) {
            
                $('html, body').animate({scrollTop: b.top - 49}, duration);
            }
            else{
                $('html, body').animate({scrollTop: b.top - 75}, duration);
            }
            
        if ($(window).width() < 760) {
            menuHide();
           
        }
        
    });
    $(this).scrollTop(0);
    $( window ).resize(function() {

        if ($(window).width() < 760) {
            
           // $('.menu').addClass('burgger-menu');
           // $('.menu').addClass('slideOutRight').removeClass('slideInRight');
            //$('.header-strip').addClass('stickyheader');
    
        } else {
           // $('.menu').removeClass('burgger-menu');
           // $('.menu').addClass('slideInRight').removeClass('slideOutRight');
            //$('.header-strip').removeClass('stickyheader');
    
        }
        
    });
    
    if ($(window).width() < 760) {
        
        $('.menu').addClass('burgger-menu');
        //$('.header-strip').addClass('stickyheader');
        $('.menu').addClass('slideOutRight');

    } else {
        $('.menu').removeClass('burgger-menu');
        //$('.header-strip').removeClass('stickyheader');
         $('.menu').removeClass('slideOutRight');
        

    }

    $('.banner-back').height($(window).height()); // Home page banner height
    var a = $('.form-wrapper').height();
    var b = (($(window).height() - a) / 2) + 25;
    $('.form-wrapper').css('margin-top', b);
   
});
var offset = 250;
var duration = 300;
$(window).scroll(function () {
    if ($(this).scrollTop() > offset)
    {
        $('.back-to-top').fadeIn(duration);
    } else {
        $('.back-to-top').fadeOut(duration);
    }
    
    
    if ($(this).scrollTop() >= 50) {                 
	  // $('.header-strip').addClass('stickyheader'); 
	  // $('.menu-2nd-desktop').addClass('stickymenu');
    }
    else
    {
        if ($(window).width() > 760) {
           // $('.header-strip').removeClass('stickyheader');
           // $('.menu-2nd-desktop').removeClass('stickymenu');
        }
    }
});

jQuery('.back-to-top').click(function (event) {
    event.preventDefault();
    jQuery('html, body').animate({scrollTop: 0}, duration);
    return false;
});






$(document).ready(function () {
    var navListItems = $('div.setup-panel div a'),
            allWells = $('.setup-content'),
            allNextBtn = $('.nextBtn'),
            allPrevBtn = $('.prevBtn');

    allWells.hide();

    navListItems.click(function (e) {
        e.preventDefault();
        var $target = $($(this).attr('href')),
                $item = $(this);

        if (!$item.hasClass('disabled')) {
            navListItems.removeClass('btn-primary').addClass('btn-default');
            $item.addClass('btn-primary');
            allWells.hide();
            $target.show();
            $target.find('input:eq(0)').focus();
        }
    });

    allPrevBtn.click(function () {
        var curStep = $(this).closest(".setup-content"),
                curStepBtn = curStep.attr("id"),
                prevStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().prev().children("a");

        prevStepWizard.removeAttr('disabled').trigger('click');
    });

    allNextBtn.click(function () {
        var curStep = $(this).closest(".setup-content"),
                curStepBtn = curStep.attr("id"),
                nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
                curInputs = curStep.find("input[type='text'],input[type='url']"),
                isValid = true;

        $(".form-group").removeClass("has-error");
        for (var i = 0; i < curInputs.length; i++) {
            if (!curInputs[i].validity.valid) {
                isValid = false;
                $(curInputs[i]).closest(".form-group").addClass("has-error");
            }
        }

        if (isValid)
            nextStepWizard.removeAttr('disabled').trigger('click');
    });

    $('div.setup-panel div a.btn-primary').trigger('click');

    /*pop-up msg*/
    $('.pop_msg_opener').click(function (event) {
        $('.pop_msg_bkg').css('display', 'block');
        $('.pop_msg').css('display', 'block');
    });
    $('.pm_close').click(function (event) {
        $('.pop_msg_bkg').css('display', 'none');
        $('.pop_msg').css('display', 'none');
    });
    
     $( ".pringplans li:nth-child(2)" ).addClass('on');

        $( ".pringplans li" ).mouseover(function() {
        $( this ).addClass('on');
        $( "li:nth-child(2)" ).removeClass('on');
        });

        $( ".pringplans li" ).mouseout(function() {
        $( this ).removeClass('on');
        $( ".pringplans li:nth-child(2)" ).addClass('on');
        });

        $( ".pringplans li:nth-child(2)" ).mouseover(function() {
        $( this ).addClass('on');
        });
    
});