<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, viewport-fit=cover,initial-scale=1.0,user-scalable=no" />
	<title>Apol</title>
	<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
	<style>
		<?php include 'apol.css'; ?>
	</style>
</head>

<body>
	<div id="app">
		<div class="header" v-if="getSubreddit()">
			<div class="header-action left">
				<div class="header-action-inner">
					<button v-if="urlStack.length > 1" v-on:click="reddit.goBack()" v-bind:disabled="loading"><?php include 'img/chevron-left.svg'; ?></button>
				</div>
			</div>
			<div @click="toggleSubRedditSelector()" class="header-title">
				<div>/r/{{ getSubreddit() }}</div>
				<div class="icon" v-if="showSubRedditSelector"><?php include 'img/chevron-up.svg'; ?></div>
				<div class="icon" v-if="!showSubRedditSelector"><?php include 'img/chevron-down.svg'; ?></div>
			</div>
			<div class="header-action right">
				<div class="header-action-inner">
					<button v-on:click="reddit.reload()" v-bind:disabled="loading"><?php include 'img/refresh-cw.svg'; ?></button>
				</div>
			</div>
		</div>
		<div class="subreddit-selector" v-if="showSubRedditSelector">
			<div v-for="subreddit in getSubredditList()" class="subreddit-selector-item" @click="reddit.loadRedditUrl(subreddit.url)">
				<div>{{subreddit.title}}</div>
			</div>
		</div>
		<div v-if="getStories().length" class="story-list">
			<div v-for="story in getStories()" class="story" @click="(event) => reddit.goToRedditUrl(story.permalink, event)" v-if="story.title">
				<div class="story-title">
					{{story.title}}
					<span v-if="story.link_flair_text">{{story.link_flair_text}}</span>
				</div>
				<div v-if="getPreview(story)" class="story-preview">
					<!-- <div>{{preview}}</div> -->
					<img v-bind:src="getPreview(story)" />
				</div>
				<div v-if="story.url" class="story-url">
					<div><?php include 'img/link.svg'; ?></div>
					<div class="story-url-uri" @click="(event) => reddit.goToUrl(story, event)">
						<span class="story-url-uri-host">{{stripHost(story.url)}}</span>
						<span class="story-url-uri-path">{{stripPath(story.url)}}</span>
					</div>
					<div><?php include 'img/chevron-right.svg'; ?></div>
				</div>
				<div class="meta">
					<div><?php include 'img/arrow-up.svg'; ?> <span class="val">{{ formatDec(story.ups) }}</span></div>
					<div><?php include 'img/message-circle.svg'; ?> <span class="val">{{ formatDec(story.num_comments) }}</span></div>
					<div><?php include 'img/clock.svg'; ?> <span class="val">{{ formatTs(story.created_utc) }}</span></div>
				</div>
			</div>
		</div>
		<div v-if="comments && comments.length" class="comments-list">
			<div v-for="comment in comments" class="comment-wrap">
				<div v-html="recursiveCommentRendering(comment)"></div>
			</div>
		</div>
	</div>
	<div v-if="loading" class="loading-indicator">Loading…</div>
	<script>
		window.toggle = function(el) {
			el.parentElement.parentElement.classList.toggle('closed');
		}

		class reddit {
			constructor(ref) {
				this.ref = ref;
				this.ref.apiEndpoint = "/api.php";
			}
			reload() {
				this.ref.listing = [];
				this.ref.comments = [];
				this.currentUrl = this.getCurrentUrl();
				this.ref.reddit.loadRedditUrl(this.currentUrl);
			}
			goBack() {
				this.ref.urlStack.pop();
				this.ref.redditUrl = this.ref.urlStack[this.ref.urlStack.length - 1];
				this.ref.reddit.loadRedditUrl(this.ref.redditUrl);
			}
			goToRedditUrl(redditUrl, e) {
				e.preventDefault();
				e.stopPropagation();
				this.ref.reddit.loadRedditUrl(redditUrl);
				return false;
			}
			goToUrl(story, e) {
				e.preventDefault();
				e.stopPropagation();
				if (story.url ?? false) {
					window.open(story.url, '_blank');
				}
				return false;
			}
			addParamsToUrl(params, url) {
				url = url.replace(".json", "");
				let urlParts = url.split("?");
				let urlParams = new URLSearchParams(urlParts[1] ?? "");
				let newParams = new URLSearchParams(params);

				for (let pair of newParams.entries()) {
					urlParams.set(pair[0], pair[1]);
				}

				return `${urlParts[0]}.json?${urlParams.toString()}`;
			}
			getCurrentUrl() {
				return this.ref.urlStack[this.ref.urlStack.length - 1];
			}
			loadMore() {
				let self = this;

				if (this.ref.loading) {
					return;
				}

				let listing = this.ref.listing;
				let lastItem = listing[listing.length - 1];
				let lastItemName = lastItem.data.name;

				if (!lastItemName || !this.ref.redditUrl) {
					return;
				}

				let newUrl = this.addParamsToUrl({
					after: lastItemName,
					count: 100
				}, this.ref.redditUrl);

				this.addToUrlStack(newUrl);

				self.ref.redditUrl = newUrl;
				self.ref.loading = true;

				self.ref.api.request(newUrl, (data) => {
					if (data.data.children ?? false) {
						data.data.children.forEach((item) => {
							self.ref.listing.push(item);
						});
						self.ref.loading = false;
					}
				});
			}
			addToUrlStack(url) {
				this.ref.urlStack.push(url);
				// window.history.pushState({}, "", url);
			}
			loadRedditUrl(url) {
				let self = this;

				self.ref.showSubRedditSelector = false;

				this.addToUrlStack(url);

				if (url[0] === "/") {
					url = "https://www.reddit.com" + url;
				}

				if (!url.includes(".json")) {
					url += ".json";
				}

				self.ref.redditUrl = url;
				self.ref.loading = true;

				let isCommentPage = url.includes("/comments/");

				if (isCommentPage) {
					this.ref.listing = [];
					this.ref.comments = [];
				};

				self.ref.api.request(url, (data) => {
					if (isCommentPage) {
						self.ref.comments = data[1].data.children;
						self.ref.listing = [data[0].data.children[0].data];
						self.ref.scrollToTop();
					} else {
						self.ref.comments = [];
						self.ref.listing = data.data.children ?? [];
					}

					self.ref.loading = false;
					self.ref.$forceUpdate();
				});
			}
		}

		class API {
			constructor(ref) {
				this.ref = ref;
			}
			request(url, cb) {
				let endpoint = config.apiEndpoint;
				let base64EncodedUrl = btoa(url);

				fetch(`${endpoint}?action=get-url&url=${base64EncodedUrl}`)
					.then((response) => response.json())
					.then(cb);
			}
		}

		class guiEvents {
			constructor(ref) {
				this.ref = ref;
				this.registerEvents();
			}
			handleScroll(event) {
				let scrollTop = window.scrollY;
				let scrollHeight = document.body.scrollHeight;
				let clientHeight = document.documentElement.clientHeight;
				let scrollPercentage =
					(scrollTop / (scrollHeight - clientHeight)) * 100;

				if (scrollPercentage > 90) {
					this.ref.reddit.loadMore();
				}
			}
			registerEvents() {
				window.addEventListener("scroll", (event) => {
					this.handleScroll(event);
				});
			}
		}

		var config = {
			apiEndpoint: "api.php",
			defaultRedditUrl: "https://www.reddit.com/r/worldnews.json",
		};

		if (window.location.hostname != "localhost") {
			config.apiEndpoint = "https://endtime-instruments.org/apol/api.php";
		}

		var app = new Vue({
			el: "#app",
			data: {
				urlStack: [],
				redditUrl: false,
				listing: [],
				comments: [],
				showSubRedditSelector: false,
				loading: false,
				activeSubreddit: false,
			},
			mounted: function() {
				this.ref = this;
				this.reddit = new reddit(this.ref);
				this.api = new API(this.ref);
				this.guiEvents = new guiEvents(this.ref);

				this.reddit.loadRedditUrl(config.defaultRedditUrl);;
			},
			methods: {
				getPreview: function(story) {
					if (story.url.includes("i.redd.it")) {
						return story.url;
					}
					return false;
				},
				getSubreddit: function() {
					if (!this.redditUrl) {
						return false;
					}

					let urlWithoutParams = this.redditUrl.split("?")[0];
					let urlParts = urlWithoutParams.split("/");

					for (let i = 0; i < urlParts.length; i++) {
						if (urlParts[i] === "r") {
							return urlParts[i + 1].replace(".json", "");
						}
					}
				},
				getStories: function() {
					let stories = [];

					console.log(this.listing);

					this.listing.forEach((story) => {
						if (story.name ?? false) {
							stories.push(story);
							return;
						};
						if (story.data ?? false) {
							stories.push(story.data);
							return;
						}
					});

					return stories;
				},
				getSubredditList() {
					return [{
							title: "WorldNews",
							url: "/r/worldnews.json"
						},
						{
							title: "Wikipedia",
							url: "/r/wikipedia.json"
						},
						{
							title: "offbeat",
							url: "/r/offbeat.json"
						},
						{
							title: "LateStageCapitalism",
							url: "/r/LateStageCapitalism.json"
						},
						{
							title: "AskHistorians",
							url: "/r/AskHistorians.json"
						},
						{
							title: "PublicFreakout",
							url: "/r/PublicFreakout.json"
						},
						{
							title: "ifyoulikeblank",
							url: "/r/ifyoulikeblank.json"
						},
						{
							title: "dataisbeautiful",
							url: "/r/dataisbeautiful.json"
						}
					];
				},
				toggleSubRedditSelector() {
					this.showSubRedditSelector = !this.showSubRedditSelector;
					this.scrollToTop();
				},
				scrollToTop: function() {
					window.scrollTo(0, 0);
				},
				convertStringToHTML: function(string) {
					let parser = new DOMParser();
					let decodedString = parser.parseFromString(string, "text/html").documentElement.textContent;
					return decodedString;
				},
				recursiveCommentRendering: function(comment) {
					let html = "";
					let classes = "";
					let level = comment.data.depth;

					if (level > 3) {
						return "";
					}

					if (typeof comment.data.author === "undefined") {
						return "";
					}

					if (comment.data.author == 'AutoModerator') {
						classes += "closed ";
					}

					html += "<div class='comment " + classes + "'>";
					html += "<div class='comment-container'>";
					html += "<div onclick='toggle(this)' class='comment-author'>" + comment.data.author + " <span>" + this.formatDec(comment.data.ups) + "</span></div>";
					html += "<div class='comment-body'>" + this.convertStringToHTML(comment.data.body_html) + "</div>";
					html += "</div>";
					html += "<div class='comment-replies'>";

					if (comment.data.replies) {
						comment.data.replies.data.children.forEach((reply) => {
							html += this.recursiveCommentRendering(reply);
						});
					}
					html += "</div>";

					return html;
				},
				stripHost: function(url) {
					let u = new URL(url);
					return u.hostname;
				},
				stripPath: function(url) {
					let u = new URL(url);
					return u.pathname;
				},
				formatDec: function(val) {
					if (val < 1000) {
						return val;
					}
					return (val / 1000).toFixed(1) + "k";
				},
				formatTs: function(val) {
					// set timezones
					return moment.unix(val).utc().fromNow();
				},
			}
		});
	</script>
</body>

</html>