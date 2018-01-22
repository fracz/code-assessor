<template>
  <div class="vbox viewport">
    <header>
      <div style="position: absolute">
        <a href="/">Quit (Esc)</a>
      </div>
      <div class="stats">
        Assessed: {{ stats.assessed }}
        <label class="input-control radio small-check">
          <input type="radio" value="unified" v-model="diffStyle">
          <span class="check"></span>
          <span class="caption">Unified</span>
        </label>
        <label class="input-control radio small-check">
          <input type="radio" value="side-by-side" v-model="diffStyle">
          <span class="check"></span>
          <span class="caption">Side by side</span>
        </label>
      </div>
      <h1 class="leader align-center">Which code is better?</h1>
    </header>
    <section class="main hbox space-between">
      <article v-html="unifiedDiff" v-show="diffStyle == 'unified'" id="unified"></article>
      <article v-html="sideBySideDiff" v-show="diffStyle != 'unified'" id="sideBySide"></article>
    </section>
    <footer>
      <div class="grid">
        <div class="row cells3">
          <div class="cell align-center">
            <button :class="'button large-button success ' + (currentAssessment === -1 ? 'chosen' : '')"
                    @click="assess(-1)" :disabled="currentAssessment !== undefined || timePassed < 5">
              The left one is better
              <small>(&larr;)</small>
            </button>
          </div>
          <div class="cell align-center">
            <button :class="'button large-button warning ' + (currentAssessment === 0 ? 'chosen' : '')"
                    @click="assess(0)" :disabled="currentAssessment !== undefined || timePassed < 5">
              ¯\_(ツ)_/¯
              <small>(&darr;)</small>
            </button>
          </div>
          <div class="cell align-center">
            <button :class="'button large-button success ' + (currentAssessment === 1 ? 'chosen' : '')"
                    @click="assess(1)" :disabled="currentAssessment !== undefined || timePassed < 5">
              The right one is better
              <small>(&rarr;)</small>
            </button>
          </div>
        </div>
      </div>
      <div class="progress small" data-role="progress">
        <div class="bar default" :style="{width: progressWidth}"></div>
      </div>
    </footer>
    <div data-role="dialog" id="dialogIdle" data-overlay="true" data-overlay-color="op-dark" data-type="alert">
      <h1>Are you paying attention?</h1>
      <h4>
        We have noticed you did not assess the third example in a row.
      </h4>
      <a class="button alert" href="https://stackoverflow.com/">You're right. Take me away from here.</a>
      <button class="button primary" @click="assess(0)">I'm here, let me continue the experiment</button>
    </div>
    <div data-role="dialog" id="dialogWrongTest" data-overlay="true" data-overlay-color="op-dark" data-type="alert">
      <h1>Are you paying attention?</h1>
      <h4>This was only a test to ensure you are reading the code. Most of the reviewers marked this example
        differently.</h4>
      <h4>While we know the code quality is strongly opinion based, this mechanism exist to prevent us from random,
        unthoughtful input.</h4>
      <h4>The next failed test will result in 10-minutes ban from this experiment.</h4>
      <button class="button primary" @click="fetch()">Got it!</button>
    </div>
    <div data-role="dialog" id="dialogBan" data-overlay="true" data-overlay-color="op-dark" data-type="alert">
      <h1>You have been banned for 10 minutes!</h1>
      <h4>You have failed two random tests in a row which resulted in a temporary ban from this experiment.</h4>
      <h4>Come back in a while to continue.</h4>
      <h4>Please, forgive us if you have been banned mistakenly.</h4>
    </div>
  </div>
</template>

<script>
  import "diff2html/dist/diff2html.min.css";
  import "diff2html/dist/diff2html.min.js";
  import "diff2html/dist/diff2html-ui.min.js";

  const TIME_LIMIT = 60;
  const MIN_TEST_PER_TASKS = 15;

  export default {
    name: 'Comparator',
    data() {
      return {
        diffId: undefined,
        unifiedDiff: '',
        sideBySideDiff: '',
        diffStyle: 'side-by-side',
        timePassed: 0,
        idleCounter: 0,
        testProbability: 0,
        testNextFailBan: false,
        testing: false,
        testExpectation: undefined,
        currentAssessment: undefined,
        skipIds: [],
        stats: {
          assessed: 0
        },
        respondentId: 0
      }
    },
    mounted() {
      window.addEventListener('keyup', (event) => {
        this.keyUp(event);
      });
      this.stats.assessed = this.$localStorage.get('assessed');
      this.skipIds = (this.$localStorage.get('skipIds') || '').split(',').filter(a => !!a);
      if (this.$localStorage.get('banUntil') > new Date().getTime()) {
        setTimeout(() => metroDialog.open('#dialogBan'));
      } else {
        this.fetch();
      }
      this.respondentId = this.$localStorage.get('respondentId');
    },
    methods: {
      fetch() {
        metroDialog.close('#dialogWrongTest');
        this.stopTimer();
        this.testing = Math.random() < (this.testProbability / MIN_TEST_PER_TASKS);
        return this.$http.get('code/' + (this.testing ? 'test' : 'random') + '?skipIds=' + this.skipIds.join(',')).then(({body}) => {
          const diff = new Diff2HtmlUI({diff: body.diff});
          this.diffId = body.id;
          this.testExpectation = body.positive;
          this.skipIds.push(this.diffId);
          this.$localStorage.set('skipIds', this.skipIds.join(','));
          diff.draw('#unified', {});
          diff.draw('#sideBySide', {
            inputFormat: 'json',
            outputFormat: 'side-by-side',
            matching: 'lines',
            synchronisedScroll: true
          });
          diff.highlightCode('#unified');
          diff.highlightCode('#sideBySide');
          this.startTimer();
        });
      },
      assess(score, clearIdle = true, forSure = false) {
        if (this.timePassed < 5) {
          return;
        }
        if (clearIdle) {
          this.idleCounter = 0;
          metroDialog.close('#dialogIdle');
        }
        const time = this.timePassed;
        this.stopTimer();
        if (score != 0) {
          this.$localStorage.set('assessed', ++this.stats.assessed);
        }
        this.currentAssessment = score;
        if (this.testing) {
          const chosen = score > 0;
          if (score != 0 && chosen != this.testExpectation) {
            if (this.testNextFailBan) {
              metroDialog.open('#dialogBan');
              this.$localStorage.set('banUntil', new Date().getTime() + 600000);
            } else {
              this.testProbability = Math.round(this.testProbability / 2);
              metroDialog.open('#dialogWrongTest');
              this.testNextFailBan = true;
            }
            this.currentAssessment = undefined;
          } else {
            if (score != 0) {
              // test passed
              this.testProbability = 0;
              this.testNextFailBan = false;
            }
            setTimeout(() => {
              this.fetch().then(() => this.currentAssessment = undefined);
            }, 200);
          }
        } else {
          ++this.testProbability;
          const request = {score, respondentId: this.respondentId, time};
          if (forSure) {
            request.forSure = true;
          }
          this.$http.put('code/' + this.diffId, request)
            .then(() => setTimeout(() => {
              this.fetch().then(() => this.currentAssessment = undefined);
            }, 200));
        }
      },
      stopTimer() {
        clearInterval(this.timer);
        this.timePassed = 0;
      },
      startTimer() {
        this.timer = setInterval(() => this.increaseTimer(), 1000);
      },
      increaseTimer() {
        ++this.timePassed;
        if (this.timePassed > TIME_LIMIT) {
          if (this.idleCounter < 2) {
            this.assess(0, false);
            ++this.idleCounter;
          } else {
            metroDialog.open('#dialogIdle')
          }
        }
      },
      keyUp(event) {
        if (event.keyCode == 37) {
          this.assess(-1, true, event.shiftKey);
        }
        if (event.keyCode == 39) {
          this.assess(1, true, event.shiftKey);
        }
        if (event.keyCode == 40) {
          this.assess(0, true, event.shiftKey);
        }
        if (event.keyCode == 27) {
          window.location.assign('/');
        }
      }
    },
    computed: {
      progressWidth() {
        return Math.round(this.timePassed * 100 / TIME_LIMIT) + '%';
      }
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style>
  /*pre {*/
  /*white-space: pre-wrap;*/
  /*}*/

  /* some basic styles. nothing to do with flexbox */
  header, footer,
  nav, article, aside {
    /*border: 1px solid black;*/
    padding: 0.25em;
    margin: 0.25em;
    border-radius: 0.25em;
  }

  .d2h-file-header {
    display: none;
  }

  /*
    Force full width & height.

    If this block is removed, the layout height/length will be determined by
    the amount of content in the page. That might result in a page which has
    a footer only a few inches from the top of the viewport, or one which
    scrolls beyond the viewport.

    This forces the layout to always be full screen regardless of how much,
    or how little, content is in place. Neither is "right" or "wrong", there
    are valid cases for each. I just want to be clear what's controlling the
    page/viewport height.
  */
  .viewport {
    width: 100%;
    height: 100%;
    margin: 0;
  }

  .bar {
    transition: width 1s linear;
  }

  .op-dark {
    background: rgba(29, 29, 29, 0.7);
  }

  .dialog {
    padding: 15px;
  }

  /* encapsulate the various syntax in helper clases */
  /* inspired by http://infrequently.org/2009/08/css-3-progress/ */

  /* items flex/expand vertically */
  .vbox {
    /* previous syntax */
    display: -webkit-box;
    display: -moz-box;
    display: box;

    -webkit-box-orient: vertical;
    -moz-box-orient: vertical;
    -ms-box-orient: vertical;
    box-orient: vertical;

    /* current syntax */
    display: -webkit-flex;
    display: -moz-flex;
    display: -ms-flex;
    display: flex;

    -webkit-flex-direction: column;
    -moz-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
  }

  /* items flex/expand horizontally */
  .hbox {
    /* previous syntax */
    display: -webkit-box;
    display: -moz-box;
    display: -ms-box;
    display: box;

    -webkit-box-orient: horizontal;
    -moz-box-orient: horizontal;
    -ms-box-orient: horizontal;
    box-orient: horizontal;

    /* current syntax */
    display: -webkit-flex;
    display: -moz-flex;
    display: -ms-flex;
    display: flex;

    -webkit-flex-direction: row;
    -moz-flex-direction: row;
    -ms-flex-direction: row;
    flex-direction: row;
  }

  .space-between {
    /* previous syntax */
    -webkit-box-pack: justify;
    -moz-box-pack: justify;
    -ms-box-pack: justify;
    box-pack: justify;

    /* current syntax */
    -webkit-justify-content: space-between;
    -moz-justify-content: space-between;
    -ms-justify-content: space-between;
    justify-content: space-between;
  }

  /* I went with a fixed height header & footer because it's a common case.
    This could easily be altered to flex proportionally with the page.
  */
  header {
    height: 70px;
  }

  footer {
    height: 80px;
  }

  footer .button {
    font-size: 1.5em;
  }

  footer .button[disabled]:not(.chosen) {
    border-color: #AAA !important;;
    background: #AAA !important;
  }

  .main {
    /* previous syntax */
    -webkit-box-flex: 1;
    -moz-box-flex: 1;
    -ms-box-flex: 1;
    box-flex: 1;

    /* current syntax */
    -webkit-flex: 1;
    -moz-flex: 1;
    -ms-flex: 1;
    flex: 1;

    position: relative;
    overflow-y: auto;
  }

  article {
    /* previous syntax */
    -webkit-box-flex: 5;
    -moz-box-flex: 5;
    -ms-box-flex: 5;
    box-flex: 5;

    /* current syntax */
    -webkit-flex: 5;
    -moz-flex: 5;
    -ms-flex: 5;
    flex: 5;
  }

  aside, nav {
    /* previous syntax */
    -webkit-box-flex: 1;
    -moz-box-flex: 1;
    -ms-box-flex: 1;
    box-flex: 1;

    /* current syntax */
    -webkit-flex: 1;
    -moz-flex: 1;
    -ms-flex: 1;
    flex: 1;
  }

  .stats {
    position: absolute;
    right: 10px;
  }

  del, ins {
    background-color: lightblue !important;
  }

  .d2h-ins, .d2h-del {
    background-color: #e1f4f7 !important;
    border-color: lightblue !important;
  }

  #sideBySide .d2h-code-line-prefix {
    visibility: hidden;
  }
</style>
