<template>
  <div class="vbox viewport">
    <header>
      <div class="place-right">
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
      <h1 class="leader align-center">Does this change improve the code?</h1>
    </header>
    <section class="main hbox space-between">
      <article v-html="unifiedDiff" v-show="diffStyle == 'unified'" id="unified"></article>
      <article v-html="sideBySideDiff" v-show="diffStyle != 'unified'" id="sideBySide"></article>
    </section>
    <footer>
      <div class="grid">
        <div class="row cells3">
          <div class="cell align-center">
            <button class="button large-button danger" @click="assess(-1)">
              <span class="mif-thumbs-down"></span>
              No, it makes the code worse
              <small>(&darr;)</small>
            </button>
          </div>
          <div class="cell align-center">
            <button class="button large-button warning" @click="assess(0)">
              <span class="mif-question"></span>
              This change does not change the quality of the code
              <small>(Esc)</small>
            </button>
          </div>
          <div class="cell align-center">
            <button class="button large-button success" @click="assess(1)">
              <span class="mif-thumbs-up"></span>
              Yes, the code is better after this change!
              <small>(&uarr;)</small>
            </button>
          </div>
        </div>
      </div>
      <div class="progress small" data-role="progress">
        <div class="bar default" style="width: 80%"></div>
      </div>
    </footer>
  </div>
</template>

<script>
  import "diff2html/dist/diff2html.min.css";
  import "diff2html/dist/diff2html.min.js";
  import "diff2html/dist/diff2html-ui.min.js";

  export default {
    name: 'HelloWorld',
    data() {
      return {
        diffId: undefined,
        unifiedDiff: 'aa',
        sideBySideDiff: 'aa',
        diffStyle: 'side-by-side'
      }
    },
    mounted() {


      this.fetch();
//      this.unifiedDiff = Diff2Html.getPrettyHtml(code);
//      this.sideBySideDiff = Diff2Html.getPrettyHtml(code, {
//          outputFormat: 'side-by-side'
//        }
//      );
    },
    methods: {
      fetch() {
        this.$http.get('code/random').then(({body}) => {
          const diff = new Diff2HtmlUI({diff: body.diff});
          this.diffId = body.id;
          diff.draw('#unified', {});
          diff.draw('#sideBySide', {
            inputFormat: 'json',
            outputFormat: 'side-by-side',
            matching: 'lines',
            synchronisedScroll: true
          });
          diff.highlightCode('#unified');
          diff.highlightCode('#sideBySide');
        });
      },
      assess(score) {
        this.$http.put('code/' + this.diffId, {score}).then(() => this.fetch());
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
</style>
