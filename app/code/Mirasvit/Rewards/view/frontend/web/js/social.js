require([
        "jquery",
        "mage/mage",
        'prototype'
    ], function($) {
        window.FORM_KEY = $('input[name="form_key"]').val();

        $().ready(function() {

            $('.rewards-tweet').bind('click', function() {
                $('#hdmx__contact-popup').show();
                $('#helpdesk-contact-form-overlay').show();
            });

            $('#hdmx__contact-popup .close').bind('click', function() {
                $('#hdmx__contact-popup').hide();
                $('#helpdesk-contact-form-overlay').hide();
            });

            $('#hdmx-submit-btn').click(function() {
                var form = $(this).parents('form');
                var el = this;
                $.ajax({
                    url: rewardsTwitterUrl,
                    data: {
                        url: rewardsCurrentUrl,
                        message: $('#message', form).val(),
                    },
                    dataType: 'json',
                    method: 'POST'
                }).done(function(data) {
                    if (typeof data.errors != 'undefined') {
                        $.each(data.errors, function () {
                            $('#message-error', form).remove();
                            $(el).after('<div for="message" generated="true" class="mage-error" id="message-error">' +
                                this + '</div>');
                        });
                    }
                    else {
                        $('#hdmx__contact-popup .close').click();
                        $('#status-message').html(data.message);
                        $('#pinterest-message').html('');
                    }
                });

                return false;
            });

            if (typeof rewardsCurrentTwiiterUrl != 'undefined') {
                var text = rewardsDefaultTwitterText + ' ' + rewardsCurrentTwiiterUrl;
                $('.mst-rewardssocial-tweet').attr('data-text', text);
            }

            if ($('.twitter-share-button.mst-rewardssocial-tweet').length) {
                $("body").on("click", "a.twitter-share-button.mst-rewardssocial-tweet", function(e) {
                    e.preventDefault();
                    tweet();
                    window.location = $(this).attr('href');
                });
            }
            $('div.column.main, div.page-wrapper').on('contentUpdated', function () {
                if (typeof rewardsCurrentTwiiterUrl != 'undefined') {
                    var text = rewardsDefaultTwitterText + ' ' + rewardsCurrentTwiiterUrl;
                    $('.mst-rewardssocial-tweet').attr('data-text', text);
                }
                if (typeof window.twttr != 'undefined' && typeof window.twttr.widgets != 'undefined') {
                    window.twttr.widgets.load();
                }
                if (typeof FB != 'undefined') {
                    FB.XFBML.parse();
                }
                if (typeof gapi != 'undefined') {
                    gapi.plusone.go('.buttons-googleplus-one');
                }
            });
            if ($('.buttons-twitter-like').length || $('.twitter-share-button').length) {
                window.twttr = (function (d, s, id) {
                    var js, fjs = d.getElementsByTagName(s)[0],
                        t = window.twttr || {};
                    if (d.getElementById(id)) {
                        return t;
                    }
                    js = d.createElement(s);
                    js.id = id;
                    js.async = 1;
                    js.src = "https://platform.twitter.com/widgets.js";
                    fjs.parentNode.insertBefore(js, fjs);

                    t._e = [];
                    t.ready = function (f) {
                        t._e.push(f);
                    };

                    return t;
                }(document, "script", "twitter-wjs"));

                twttr.ready(function (twttr) {
                    twttr.events.bind('tweet', function (a) {
                        if (!a) {
                            return;
                        }
                        if (a.target.offsetParent.hasClassName('rewardssocial-buttons')) {
                            tweet();
                        }
                    });
                });
            }

            // google+
            //!function (d, s, id) {
            //    var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
            //    if (!d.getElementById(id)) {
            //        js = d.createElement(s);
            //        js.id = id;
            //        js.src = p + '://apis.google.com/js/platform.js';
            //        fjs.parentNode.insertBefore(js, fjs);
            //    }
            //}(document, 'script', 'gplus-script');

            if (typeof rewardsOneUrl != 'undefined') {
                RewardsGoogleplusOne = Class.create();
                RewardsGoogleplusOne.prototype = {
                    initialize: function (url) {
                        this.url = url;
                        return this;
                    },
                    onPlus: function () {
                        new Ajax.Request(this.url, {
                            method: "get",
                            parameters: {},
                            onSuccess: this.onSuccess.bind(this),
                        });
                        return this;
                    },
                    onUnPlus: function () {
                        return this;
                    },
                    onSuccess: function (response) {
                        $('#status-message').html(response.responseText);
                        $('#googleplus-message').html('');
                        return this;
                    }
                };

                var gp = new RewardsGoogleplusOne(rewardsOneUrl + '?url=' + rewardsCurrentUrl);
                rewardsGoogleplusEvent = function (event) {
                    if (event.state == 'on') {
                        gp.onPlus()
                    } else if (event.state == 'off') {
                        gp.onUnPlus()
                    }
                };
            }

            // pinterest
            $("#buttons-pinterest-pin").on("click", "a", pinIt);
            $("body").on("click", "#rewards_fb_share", function(e) {
                FB.ui({
                    method: 'share',
                    display: 'popup',
                    href: rewardsShareCurrentUrl,
                }, function(response){
                    if (typeof response !== 'undefined') {
                        fbShare();
                    }
                });
            });
        });
    }
);

window.onload = function() {
    if (jQuery('.buttons-facebook-like').length || jQuery('.buttons-fb-share').length || jQuery('.fb-share-button').length) {
        // facebook
        (function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
            if (d.getElementById(id)) {
                return;
            }
            js = d.createElement(s);
            js.id = id;
            js.async = true;
            js.src = p + "://connect.facebook.net/" + fbLocaleCode + "/sdk.js#xfbml=1&appId=" + fbAppId + "&version=v2.0";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    }

    if (jQuery('.g-plusone').length) {
        (function () {
            var po = document.createElement('script');
            po.type = 'text/javascript';
            po.async = true;
            po.src = 'https://apis.google.com/js/platform.js';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(po, s);
        })();
    }
};

window.oldFbInit = window.fbAsyncInit || function(){};
window.fbAsyncInit = function() {
    window.oldFbInit();
    FB.Event.subscribe('xfbml.render', function(b){
        FB.Event.subscribe('edge.create', fbLike);
        FB.Event.subscribe('edge.remove', fbUnlike);
    });
};

var addThisTimerCounter = 0;
var addThisTimer = setInterval( function() {
    addThisTimerCounter++;
    if ( typeof addthis !== 'undefined' ) {
        clearInterval( addThisTimer );
        addthis.addEventListener('addthis.menu.share', addthisShare);
    } else if (addThisTimerCounter > 40) { // wait 4sec
        clearInterval( addThisTimer );
    }
}, 100 );

function fbShare() {
    new Ajax.Request(window.fbShareUrl + '?url=' + rewardsCurrentUrl, {
        onSuccess: function(response) {
            jQuery('#status-message').html(response.responseText);
            jQuery('#facebook-share-message').html('');
        }
    });
}

function fbLike() {
    new Ajax.Request(window.fbLikeUrl + '?url=' + rewardsCurrentUrl, {
        onSuccess: function(response) {
            jQuery('#status-message').html(response.responseText);
            jQuery('#facebook-message').html('');
        }
    });
}

function fbUnlike() {
    new Ajax.Request(window.fbUnlikeUrl + '?url=' + rewardsCurrentUrl, {
        onSuccess: function(response) {
            jQuery('#status-message').html(response.responseText);
            jQuery('#facebook-message').html('');
        }
    });
}

function tweet() {
    new Ajax.Request(rewardsTwitterUrl + '?url=' + rewardsCurrentTwiiterUrl, {
        onSuccess: function(response) {
            jQuery('#status-message').html(response.responseText);
            jQuery('#twitter-message').html('');
        }
    });
}
function googlePlus() {
    new Ajax.Request(rewardsOneUrl + '?url=' + rewardsCurrentUrl, {
        onSuccess: function(response) {
            jQuery('#status-message').html(response.responseText);
            jQuery('#googleplus-message').html('');
        }
    });
}

function pinIt() {
    new Ajax.Request(rewardsPinUrl + '?url=' + rewardsCurrentUrl, {
        onSuccess: function(response) {
            jQuery('#status-message').html(response.responseText);
            jQuery('#pinterest-message').html('');
        }
    });
}

function addthisShare(e) {
    if (e.type == 'addthis.menu.share') {
        switch (e.data.service) {
            case "facebook":
                fbLike();
                break;
            case "twitter":
                tweet();
                break;
            case "google_plusone_share":
                googlePlus();
                break;
            case "pinterest_share":
                pinIt();
                break;
        }
    }
}

