/*!
 * Name    : Video Background Extension for Jarallax
 * Version : 1.0.1
 * Author  : nK <https://nkdev.info>
 * GitHub  : https://github.com/nk-o/jarallax
 */
!function(o) {
    var i = {};
    function n(e) {
        if (i[e])
            return i[e].exports;
        var t = i[e] = {
            i: e,
            l: !1,
            exports: {}
        };
        return o[e].call(t.exports, t, t.exports, n),
        t.l = !0,
        t.exports
    }
    n.m = o,
    n.c = i,
    n.d = function(e, t, o) {
        n.o(e, t) || Object.defineProperty(e, t, {
            enumerable: !0,
            get: o
        })
    }
    ,
    n.r = function(e) {
        "undefined" != typeof Symbol && Symbol.toStringTag && Object.defineProperty(e, Symbol.toStringTag, {
            value: "Module"
        }),
        Object.defineProperty(e, "__esModule", {
            value: !0
        })
    }
    ,
    n.t = function(t, e) {
        if (1 & e && (t = n(t)),
        8 & e)
            return t;
        if (4 & e && "object" == typeof t && t && t.__esModule)
            return t;
        var o = Object.create(null);
        if (n.r(o),
        Object.defineProperty(o, "default", {
            enumerable: !0,
            value: t
        }),
        2 & e && "string" != typeof t)
            for (var i in t)
                n.d(o, i, function(e) {
                    return t[e]
                }
                .bind(null, i));
        return o
    }
    ,
    n.n = function(e) {
        var t = e && e.__esModule ? function() {
            return e.default
        }
        : function() {
            return e
        }
        ;
        return n.d(t, "a", t),
        t
    }
    ,
    n.o = function(e, t) {
        return Object.prototype.hasOwnProperty.call(e, t)
    }
    ,
    n.p = "",
    n(n.s = 6)
}([, , function(e, t, o) {
    "use strict";
    e.exports = function(e) {
        "complete" === document.readyState || "interactive" === document.readyState ? e.call() : document.attachEvent ? document.attachEvent("onreadystatechange", function() {
            "interactive" === document.readyState && e.call()
        }) : document.addEventListener && document.addEventListener("DOMContentLoaded", e)
    }
}
, , function(o, e, t) {
    "use strict";
    (function(e) {
        var t;
        t = "undefined" != typeof window ? window : void 0 !== e ? e : "undefined" != typeof self ? self : {},
        o.exports = t
    }
    ).call(this, t(5))
}
, function(e, t, o) {
    "use strict";
    var i, n = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(e) {
        return typeof e
    }
    : function(e) {
        return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e
    }
    ;
    i = function() {
        return this
    }();
    try {
        i = i || Function("return this")() || (0,
        eval)("this")
    } catch (e) {
        "object" === ("undefined" == typeof window ? "undefined" : n(window)) && (i = window)
    }
    e.exports = i
}
, function(e, t, o) {
    e.exports = o(7)
}
, function(e, t, o) {
    "use strict";
    var i = l(o(8))
      , n = l(o(4))
      , a = l(o(2))
      , r = l(o(10));
    function l(e) {
        return e && e.__esModule ? e : {
            default: e
        }
    }
    n.default.VideoWorker = n.default.VideoWorker || i.default,
    (0,
    r.default)(),
    (0,
    a.default)(function() {
        "undefined" != typeof jarallax && jarallax(document.querySelectorAll("[data-jarallax-video]"))
    })
}
, function(e, t, o) {
    "use strict";
    e.exports = o(9)
}
, function(e, t, o) {
    "use strict";
    Object.defineProperty(t, "__esModule", {
        value: !0
    });
    var n = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(e) {
        return typeof e
    }
    : function(e) {
        return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e
    }
      , a = function() {
        function i(e, t) {
            for (var o = 0; o < t.length; o++) {
                var i = t[o];
                i.enumerable = i.enumerable || !1,
                i.configurable = !0,
                "value"in i && (i.writable = !0),
                Object.defineProperty(e, i.key, i)
            }
        }
        return function(e, t, o) {
            return t && i(e.prototype, t),
            o && i(e, o),
            e
        }
    }();
    function i() {
        this._done = [],
        this._fail = []
    }
    i.prototype = {
        execute: function(e, t) {
            var o = e.length;
            for (t = Array.prototype.slice.call(t); o--; )
                e[o].apply(null, t)
        },
        resolve: function() {
            this.execute(this._done, arguments)
        },
        reject: function() {
            this.execute(this._fail, arguments)
        },
        done: function(e) {
            this._done.push(e)
        },
        fail: function(e) {
            this._fail.push(e)
        }
    };
    var r = 0
      , l = 0
      , u = 0
      , p = 0
      , d = 0
      , s = new i
      , y = new i
      , c = function() {
        function i(e, t) {
            !function(e, t) {
                if (!(e instanceof t))
                    throw new TypeError("Cannot call a class as a function")
            }(this, i);
            var o = this;
            o.url = e,
            o.options_default = {
                autoplay: !1,
                loop: !1,
                mute: !1,
                volume: 100,
                showContols: !0,
                startTime: 0,
                endTime: 0
            },
            o.options = o.extend({}, o.options_default, t),
            o.videoID = o.parseURL(e),
            o.videoID && (o.ID = r++,
            o.loadAPI(),
            o.init())
        }
        return a(i, [{
            key: "extend",
            value: function(o) {
                var i = arguments;
                return o = o || {},
                Object.keys(arguments).forEach(function(t) {
                    i[t] && Object.keys(i[t]).forEach(function(e) {
                        o[e] = i[t][e]
                    })
                }),
                o
            }
        }, {
            key: "parseURL",
            value: function(e) {
                var t, o, i, n, a, r = !(!(t = e.match(/.*(?:youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=)([^#\&\?]*).*/)) || 11 !== t[1].length) && t[1], l = !(!(o = e.match(/https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)/)) || !o[3]) && o[3], u = (i = e.split(/,(?=mp4\:|webm\:|ogv\:|ogg\:)/),
                n = {},
                a = 0,
                i.forEach(function(e) {
                    var t = e.match(/^(mp4|webm|ogv|ogg)\:(.*)/);
                    t && t[1] && t[2] && (n["ogv" === t[1] ? "ogg" : t[1]] = t[2],
                    a = 1)
                }),
                !!a && n);
                return r ? (this.type = "youtube",
                r) : l ? (this.type = "vimeo",
                l) : !!u && (this.type = "local",
                u)
            }
        }, {
            key: "isValid",
            value: function() {
                return !!this.videoID
            }
        }, {
            key: "on",
            value: function(e, t) {
                this.userEventsList = this.userEventsList || [],
                (this.userEventsList[e] || (this.userEventsList[e] = [])).push(t)
            }
        }, {
            key: "off",
            value: function(o, i) {
                var n = this;
                this.userEventsList && this.userEventsList[o] && (i ? this.userEventsList[o].forEach(function(e, t) {
                    e === i && (n.userEventsList[o][t] = !1)
                }) : delete this.userEventsList[o])
            }
        }, {
            key: "fire",
            value: function(e) {
                var t = this
                  , o = [].slice.call(arguments, 1);
                this.userEventsList && void 0 !== this.userEventsList[e] && this.userEventsList[e].forEach(function(e) {
                    e && e.apply(t, o)
                })
            }
        }, {
            key: "play",
            value: function(e) {
                var t = this;
                t.player && ("youtube" === t.type && t.player.playVideo && (void 0 !== e && t.player.seekTo(e || 0),
                YT.PlayerState.PLAYING !== t.player.getPlayerState() && t.player.playVideo()),
                "vimeo" === t.type && (void 0 !== e && t.player.setCurrentTime(e),
                t.player.getPaused().then(function(e) {
                    e && t.player.play()
                })),
                "local" === t.type && (void 0 !== e && (t.player.currentTime = e),
                t.player.paused && t.player.play()))
            }
        }, {
            key: "pause",
            value: function() {
                var t = this;
                t.player && ("youtube" === t.type && t.player.pauseVideo && YT.PlayerState.PLAYING === t.player.getPlayerState() && t.player.pauseVideo(),
                "vimeo" === t.type && t.player.getPaused().then(function(e) {
                    e || t.player.pause()
                }),
                "local" === t.type && (t.player.paused || t.player.pause()))
            }
        }, {
            key: "mute",
            value: function() {
                var e = this;
                e.player && ("youtube" === e.type && e.player.mute && e.player.mute(),
                "vimeo" === e.type && e.player.setVolume && e.player.setVolume(0),
                "local" === e.type && (e.$video.muted = !0))
            }
        }, {
            key: "unmute",
            value: function() {
                var e = this;
                e.player && ("youtube" === e.type && e.player.mute && e.player.unMute(),
                "vimeo" === e.type && e.player.setVolume && e.player.setVolume(e.options.volume),
                "local" === e.type && (e.$video.muted = !1))
            }
        }, {
            key: "setVolume",
            value: function() {
                var e = 0 < arguments.length && void 0 !== arguments[0] && arguments[0]
                  , t = this;
                t.player && e && ("youtube" === t.type && t.player.setVolume && t.player.setVolume(e),
                "vimeo" === t.type && t.player.setVolume && t.player.setVolume(e),
                "local" === t.type && (t.$video.volume = e / 100))
            }
        }, {
            key: "getVolume",
            value: function(t) {
                var e = this;
                e.player ? ("youtube" === e.type && e.player.getVolume && t(e.player.getVolume()),
                "vimeo" === e.type && e.player.getVolume && e.player.getVolume().then(function(e) {
                    t(e)
                }),
                "local" === e.type && t(100 * e.$video.volume)) : t(!1)
            }
        }, {
            key: "getMuted",
            value: function(t) {
                var e = this;
                e.player ? ("youtube" === e.type && e.player.isMuted && t(e.player.isMuted()),
                "vimeo" === e.type && e.player.getVolume && e.player.getVolume().then(function(e) {
                    t(!!e)
                }),
                "local" === e.type && t(e.$video.muted)) : t(null)
            }
        }, {
            key: "getImageURL",
            value: function(t) {
                var o = this;
                if (o.videoImage)
                    t(o.videoImage);
                else {
                    if ("youtube" === o.type) {
                        var e = ["maxresdefault", "sddefault", "hqdefault", "0"]
                          , i = 0
                          , n = new Image;
                        n.onload = function() {
                            120 !== (this.naturalWidth || this.width) || i === e.length - 1 ? (o.videoImage = "https://img.youtube.com/vi/" + o.videoID + "/" + e[i] + ".jpg",
                            t(o.videoImage)) : (i++,
                            this.src = "https://img.youtube.com/vi/" + o.videoID + "/" + e[i] + ".jpg")
                        }
                        ,
                        n.src = "https://img.youtube.com/vi/" + o.videoID + "/" + e[i] + ".jpg"
                    }
                    if ("vimeo" === o.type) {
                        var a = new XMLHttpRequest;
                        a.open("GET", "https://vimeo.com/api/v2/video/" + o.videoID + ".json", !0),
                        a.onreadystatechange = function() {
                            if (4 === this.readyState && 200 <= this.status && this.status < 400) {
                                var e = JSON.parse(this.responseText);
                                o.videoImage = e[0].thumbnail_large,
                                t(o.videoImage)
                            }
                        }
                        ,
                        a.send(),
                        a = null
                    }
                }
            }
        }, {
            key: "getIframe",
            value: function(e) {
                this.getVideo(e)
            }
        }, {
            key: "getVideo",
            value: function(u) {
                var p = this;
                p.$video ? u(p.$video) : p.onAPIready(function() {
                    var e = void 0;
                    if (p.$video || ((e = document.createElement("div")).style.display = "none"),
                    "youtube" === p.type) {
                        p.playerOptions = {},
                        p.playerOptions.videoId = p.videoID,
                        p.playerOptions.playerVars = {
                            autohide: 1,
                            rel: 0,
                            autoplay: 0,
                            playsinline: 1
                        },
                        p.options.showContols || (p.playerOptions.playerVars.iv_load_policy = 3,
                        p.playerOptions.playerVars.modestbranding = 1,
                        p.playerOptions.playerVars.controls = 0,
                        p.playerOptions.playerVars.showinfo = 0,
                        p.playerOptions.playerVars.disablekb = 1);
                        var t = void 0
                          , o = void 0;
                        p.playerOptions.events = {
                            onReady: function(t) {
                                if (p.options.mute ? t.target.mute() : p.options.volume && t.target.setVolume(p.options.volume),
                                p.options.autoplay && p.play(p.options.startTime),
                                p.fire("ready", t),
                                p.options.loop && !p.options.endTime) {
                                    p.options.endTime = p.player.getDuration() - .1
                                }
                                setInterval(function() {
                                    p.getVolume(function(e) {
                                        p.options.volume !== e && (p.options.volume = e,
                                        p.fire("volumechange", t))
                                    })
                                }, 150)
                            },
                            onStateChange: function(e) {
                                p.options.loop && e.data === YT.PlayerState.ENDED && p.play(p.options.startTime),
                                t || e.data !== YT.PlayerState.PLAYING || (t = 1,
                                p.fire("started", e)),
                                e.data === YT.PlayerState.PLAYING && p.fire("play", e),
                                e.data === YT.PlayerState.PAUSED && p.fire("pause", e),
                                e.data === YT.PlayerState.ENDED && p.fire("ended", e),
                                e.data === YT.PlayerState.PLAYING ? o = setInterval(function() {
                                    p.fire("timeupdate", e),
                                    p.options.endTime && p.player.getCurrentTime() >= p.options.endTime && (p.options.loop ? p.play(p.options.startTime) : p.pause())
                                }, 150) : clearInterval(o)
                            }
                        };
                        var i = !p.$video;
                        if (i) {
                            var n = document.createElement("div");
                            n.setAttribute("id", p.playerID),
                            e.appendChild(n),
                            document.body.appendChild(e)
                        }
                        p.player = p.player || new window.YT.Player(p.playerID,p.playerOptions),
                        i && (p.$video = document.getElementById(p.playerID),
                        p.videoWidth = parseInt(p.$video.getAttribute("width"), 10) || 1280,
                        p.videoHeight = parseInt(p.$video.getAttribute("height"), 10) || 720)
                    }
                    if ("vimeo" === p.type) {
                        if (p.playerOptions = {
                            id: p.videoID,
                            autopause: 0,
                            transparent: 0,
                            autoplay: p.options.autoplay ? 1 : 0,
                            loop: p.options.loop ? 1 : 0,
                            muted: p.options.mute ? 1 : 0
                        },
                        p.options.volume && (p.playerOptions.volume = p.options.volume),
                        p.options.showContols || (p.playerOptions.badge = 0,
                        p.playerOptions.byline = 0,
                        p.playerOptions.portrait = 0,
                        p.playerOptions.title = 0),
                        !p.$video) {
                            var a = "";
                            Object.keys(p.playerOptions).forEach(function(e) {
                                "" !== a && (a += "&"),
                                a += e + "=" + encodeURIComponent(p.playerOptions[e])
                            }),
                            p.$video = document.createElement("iframe"),
                            p.$video.setAttribute("id", p.playerID),
                            p.$video.setAttribute("src", "https://player.vimeo.com/video/" + p.videoID + "?" + a),
                            p.$video.setAttribute("frameborder", "0"),
                            p.$video.setAttribute("mozallowfullscreen", ""),
                            p.$video.setAttribute("allowfullscreen", ""),
                            e.appendChild(p.$video),
                            document.body.appendChild(e)
                        }
                        p.player = p.player || new Vimeo(p.$video,p.playerOptions),
                        p.options.startTime && p.options.autoplay && p.player.setCurrentTime(p.options.startTime),
                        p.player.getVideoWidth().then(function(e) {
                            p.videoWidth = e || 1280
                        }),
                        p.player.getVideoHeight().then(function(e) {
                            p.videoHeight = e || 720
                        });
                        var r = void 0;
                        p.player.on("timeupdate", function(e) {
                            r || (p.fire("started", e),
                            r = 1),
                            p.fire("timeupdate", e),
                            p.options.endTime && p.options.endTime && e.seconds >= p.options.endTime && (p.options.loop ? p.play(p.options.startTime) : p.pause())
                        }),
                        p.player.on("play", function(e) {
                            p.fire("play", e),
                            p.options.startTime && 0 === e.seconds && p.play(p.options.startTime)
                        }),
                        p.player.on("pause", function(e) {
                            p.fire("pause", e)
                        }),
                        p.player.on("ended", function(e) {
                            p.fire("ended", e)
                        }),
                        p.player.on("loaded", function(e) {
                            p.fire("ready", e)
                        }),
                        p.player.on("volumechange", function(e) {
                            p.fire("volumechange", e)
                        })
                    }
                    if ("local" === p.type) {
                        p.$video || (p.$video = document.createElement("video"),
                        p.options.showContols && (p.$video.controls = !0),
                        p.options.mute ? p.$video.muted = !0 : p.$video.volume && (p.$video.volume = p.options.volume / 100),
                        p.options.loop && (p.$video.loop = !0),
                        p.$video.setAttribute("playsinline", ""),
                        p.$video.setAttribute("webkit-playsinline", ""),
                        p.$video.setAttribute("id", p.playerID),
                        e.appendChild(p.$video),
                        document.body.appendChild(e),
                        Object.keys(p.videoID).forEach(function(e) {
                            var t, o, i, n;
                            t = p.$video,
                            o = p.videoID[e],
                            i = "video/" + e,
                            (n = document.createElement("source")).src = o,
                            n.type = i,
                            t.appendChild(n)
                        })),
                        p.player = p.player || p.$video;
                        var l = void 0;
                        p.player.addEventListener("playing", function(e) {
                            l || p.fire("started", e),
                            l = 1
                        }),
                        p.player.addEventListener("timeupdate", function(e) {
                            p.fire("timeupdate", e),
                            p.options.endTime && p.options.endTime && this.currentTime >= p.options.endTime && (p.options.loop ? p.play(p.options.startTime) : p.pause())
                        }),
                        p.player.addEventListener("play", function(e) {
                            p.fire("play", e)
                        }),
                        p.player.addEventListener("pause", function(e) {
                            p.fire("pause", e)
                        }),
                        p.player.addEventListener("ended", function(e) {
                            p.fire("ended", e)
                        }),
                        p.player.addEventListener("loadedmetadata", function() {
                            p.videoWidth = this.videoWidth || 1280,
                            p.videoHeight = this.videoHeight || 720,
                            p.fire("ready"),
                            p.options.autoplay && p.play(p.options.startTime)
                        }),
                        p.player.addEventListener("volumechange", function(e) {
                            p.getVolume(function(e) {
                                p.options.volume = e
                            }),
                            p.fire("volumechange", e)
                        })
                    }
                    u(p.$video)
                })
            }
        }, {
            key: "init",
            value: function() {
                this.playerID = "VideoWorker-" + this.ID
            }
        }, {
            key: "loadAPI",
            value: function() {
            	if (this.type == "vimeo") {
	            	require(['https://player.vimeo.com/api/player.js'], function(Vimeo) {
		                if (!l || !u) {
		                	window.Vimeo = Vimeo;
		                }
	            	});
            	} else {
            		if (!l || !u) {
	                    var e = "";
	                    if ("youtube" !== this.type || l || (l = 1,
	                    e = "https://www.youtube.com/iframe_api"),
	                    "vimeo" !== this.type || u || (u = 1,
	                    e = "https://player.vimeo.com/api/player.js"),
	                    e) {
	                        var t = document.createElement("script")
	                          , o = document.getElementsByTagName("head")[0];
	                        t.src = e,
	                        o.appendChild(t),
	                        t = o = null
	                    }
	                }
            	}
            }
        }, {
            key: "onAPIready",
            value: function(e) {
                if ("youtube" === this.type && ("undefined" != typeof YT && 0 !== YT.loaded || p ? "object" === ("undefined" == typeof YT ? "undefined" : n(YT)) && 1 === YT.loaded ? e() : s.done(function() {
                    e()
                }) : (p = 1,
                window.onYouTubeIframeAPIReady = function() {
                    window.onYouTubeIframeAPIReady = null,
                    s.resolve("done"),
                    e()
                }
                )),
                "vimeo" === this.type)
                    if ("undefined" != typeof Vimeo || d)
                        "undefined" != typeof Vimeo ? e() : y.done(function() {
                            e()
                        });
                    else {
                        d = 1;
                        var t = setInterval(function() {
                            "undefined" != typeof Vimeo && (clearInterval(t),
                            y.resolve("done"),
                            e())
                        }, 20)
                    }
                "local" === this.type && e()
            }
        }]),
        i
    }();
    t.default = c
}
, function(e, t, o) {
    "use strict";
    Object.defineProperty(t, "__esModule", {
        value: !0
    }),
    t.default = function() {
        var e = 0 < arguments.length && void 0 !== arguments[0] ? arguments[0] : u.default.jarallax;
        if (void 0 === e)
            return;
        var t = e.constructor
          , i = t.prototype.init;
        t.prototype.init = function() {
            var o = this;
            i.apply(o),
            o.video && !o.options.disableVideo() && o.video.getVideo(function(e) {
                var t = e.parentNode;
                o.css(e, {
                    position: o.image.position,
                    top: "0px",
                    left: "0px",
                    right: "0px",
                    bottom: "0px",
                    width: "100%",
                    height: "100%",
                    maxWidth: "none",
                    maxHeight: "none",
                    margin: 0,
                    zIndex: -1
                }),
                o.$video = e,
                o.image.$container.appendChild(e),
                t.parentNode.removeChild(t)
            })
        }
        ;
        var l = t.prototype.coverImage;
        t.prototype.coverImage = function() {
            var e = this
              , t = l.apply(e)
              , o = !!e.image.$item && e.image.$item.nodeName;
            if (t && e.video && o && ("IFRAME" === o || "VIDEO" === o)) {
                var i = t.image.height
                  , n = i * e.image.width / e.image.height
                  , a = (t.container.width - n) / 2
                  , r = t.image.marginTop;
                t.container.width > n && (n = t.container.width,
                i = n * e.image.height / e.image.width,
                a = 0,
                r += (t.image.height - i) / 2),
                "IFRAME" === o && (i += 400,
                r -= 200),
                e.css(e.$video, {
                    width: n + "px",
                    marginLeft: a + "px",
                    height: i + "px",
                    marginTop: r + "px"
                })
            }
            return t
        }
        ;
        var o = t.prototype.initImg;
        t.prototype.initImg = function() {
            var e = this
              , t = o.apply(e);
            return e.options.videoSrc || (e.options.videoSrc = e.$item.getAttribute("data-jarallax-video") || null),
            e.options.videoSrc ? (e.defaultInitImgResult = t,
            !0) : t
        }
        ;
        var n = t.prototype.canInitParallax;
        t.prototype.canInitParallax = function() {
            var o = this
              , e = n.apply(o);
            if (!o.options.videoSrc)
                return e;
            var t = new r.default(o.options.videoSrc,{
                autoplay: !0,
                loop: o.options.videoLoop,
                showContols: !1,
                startTime: o.options.videoStartTime || 0,
                endTime: o.options.videoEndTime || 0,
                mute: o.options.videoVolume ? 0 : 1,
                volume: o.options.videoVolume || 0
            });
            if (t.isValid())
                if (e) {
                    if (t.on("ready", function() {
                        if (o.options.videoPlayOnlyVisible) {
                            var e = o.onScroll;
                            o.onScroll = function() {
                                e.apply(o),
                                (o.options.videoLoop || !o.options.videoLoop && !o.videoEnded) && (o.isVisible() ? t.play() : t.pause())
                            }
                        } else
                            t.play()
                    }),
                    t.on("started", function() {
                        o.image.$default_item = o.image.$item,
                        o.image.$item = o.$video,
                        o.image.width = o.video.videoWidth || 1280,
                        o.image.height = o.video.videoHeight || 720,
                        o.coverImage(),
                        o.clipContainer(),
                        o.onScroll(),
                        o.image.$default_item && (o.image.$default_item.style.display = "none")
                    }),
                    t.on("ended", function() {
                        o.videoEnded = !0,
                        o.options.videoLoop || o.image.$default_item && (o.image.$item = o.image.$default_item,
                        o.image.$item.style.display = "block",
                        o.coverImage(),
                        o.clipContainer(),
                        o.onScroll())
                    }),
                    o.video = t,
                    !o.defaultInitImgResult)
                        return "local" !== t.type ? (t.getImageURL(function(e) {
                            o.image.src = e,
                            o.init()
                        }),
                        !1) : (o.image.src = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7",
                        !0)
                } else
                    o.defaultInitImgResult || t.getImageURL(function(e) {
                        var t = o.$item.getAttribute("style");
                        t && o.$item.setAttribute("data-jarallax-original-styles", t),
                        o.css(o.$item, {
                            "background-image": 'url("' + e + '")',
                            "background-position": "center",
                            "background-size": "cover"
                        })
                    });
            return e
        }
        ;
        var a = t.prototype.destroy;
        t.prototype.destroy = function() {
            var e = this;
            e.image.$default_item && (e.image.$item = e.image.$default_item,
            delete e.image.$default_item),
            a.apply(e)
        }
    }
    ;
    var r = i(o(8))
      , u = i(o(4));
    function i(e) {
        return e && e.__esModule ? e : {
            default: e
        }
    }
}
]);
//# sourceMappingURL=jarallax-video.min.js.map
