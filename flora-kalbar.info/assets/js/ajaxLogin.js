$(document).ready(function() {

$('#formSignup').submit(function(event) {
    event.preventDefault();

    $.ajax({
        url: baseUrl+"login/signup",
        type: 'POST',
        data: $(this).serialize(),
        success: function(data) {
            var result = $.parseJSON(data);            
            if(result.statusName == 'exist' || result.statusEmail == 'exist' || result.statusShortname == 'exist' || result.statusTwitter == 'exist'){
                $('#signup-password,#signup-re_password').val('');
                if(result.statusName == 'exist'){
                    $('#nameGroup').append('<span class="error">'+result.msgName+'</span>');
                }
                if(result.statusEmail == 'exist'){
                    $('#emailGroup').append('<span class="error">'+result.msgEmail+'</span>');
                } 
                if(result.statusShortname == 'exist'){
                    $('#shortnameGroup').append('<span class="error">'+result.msgShortname+'</span>');
                }
                if(result.statusTwitter == 'exist'){
                    $('#twitterGroup').append('<span class="error">'+result.msgTwitter+'</span>');
                }
            }
            else{
                alert('User created, do login for enter the site.');
                location.reload();
            } 
        }
    });
    $('#signup-name').keyup(function(){
       $('#nameGroup > span').remove();
    });
    $('#signup-email').keyup(function(){
       $('#emailGroup > span').remove();
    });
    $('#signup-shortname').keyup(function(){
       $('#shortnameGroup > span').remove();
    });
    $('#signup-twitter').keyup(function(){
       $('#twitterGroup > span').remove();
    });
});

});