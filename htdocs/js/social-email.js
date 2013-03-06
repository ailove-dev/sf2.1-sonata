/**
 * Login / Auth
 */
var socialEmail = {
    redirectLocation: '/',
    init: function() {
        this.newEmail();
        this.sendConfirmSocial();
        this.popupClose();
        this.ajaxForm();
    },
    ajaxForm: function(){
        if ($('#email_add').length == 1) {
            $('#email_add').ajaxForm({ 
                success: socialEmail.showEmailAddResponse,
                beforeSubmit: function(arr, $form, options) { 
                    if(!socialEmail.validEmailAddForm()) {
                        return false;
                    }

                    if ($('#email_submit').hasClass('disabled')) {
                        return false;
                    }

                    $('#email_submit').addClass('disabled');

                    return true;
                },
                dataType:  'json'
            });
        };
        if ($('#social_send_confirm_email_form').length == 1) {
            $('#social_send_confirm_email_form').ajaxForm({ 
                success: socialEmail.showSocialSendConfirmEmailFormResponse,
                beforeSubmit: function(arr, $form, options) {
                    if ($('#social_send_confirm_email_submit').hasClass('disabled')) {
                        return false;
                    }

                    $('#social_send_confirm_email_submit').addClass('disabled');

                    return true;
                },
                dataType:  'json'
            });
        }
    },    
    showEmailAddResponse: function(data){
        if(data.status == 'ok'){
            $('#email_submit').removeClass('disabled');
            if (data.redirect.length > 0) {
                socialEmail.redirectLocation = data.redirect;
            }
            $('#emailModal').modal('hide');
            $('#confirmEmailModal').modal('show');
        } else {
            $('#email_submit').removeClass('disabled');
            $('#emailModal').modal('hide');
            $('#email_provided_label').html(data.email);
            $('#email_provided').val(data.email);
            $('#emailNotUniqModal').modal('show');
        }
    },
    showSocialSendConfirmEmailFormResponse: function(data){
        if(data.status == 'ok'){
            $('#email_submit').removeClass('disabled');
            $('#emailNotUniqModal').modal('hide');
            $('#successEmailModal').modal('show');
        } else {
            $('#social_send_confirm_email_submit').removeClass('disabled');
        }
    },
    validEmailAddForm: function(){
//            $('#email_add .error').html('&nbsp;');
        return true;
    },
    newEmail: function(){
        $('#new_email').click(function(){
            $('#emailNotUniqModal').modal('hide');
            $('#emailModal').modal('show');
            
            return false;
        });
    },
    sendConfirmSocial: function(){
        $('#send_confirm_email').click(function(){
            var email = $('#emailNotUniq .popup_hasmail_mail_the').text();
            var social = 'vk';
            $.ajax({
                url: '/user/social/confirm/send/' + email + '/' + social,
                success: function(data) {
                    $('#emailNotUniq').hide();
                    $('#successSocialConfirm').show(300);
                }
            });
            
            return false;
        });
    },
    popupClose: function() {
        $('#successSocialConfirm .popup_closethis').click(function(){
            $('#successSocialConfirm').hide();
            window.location = '/';

            return false;
        });

        $('#emailNotUniq .popup_closethis').click(function(){
            $('#emailNotUniq').hide();
            window.location = '/';

            return false;
        });

        $('#confirmEmailOrBlock .popup_closethis').click(function(){
            $('#confirmEmailOrBlock').hide();
            window.location = socialEmail.redirectLocation;

            return false;
        });
    }
}

/**
 * Ready
 */
$(function(){
    socialEmail.init();
});
