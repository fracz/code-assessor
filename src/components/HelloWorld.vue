<template>
  <div class="vbox viewport">
    <header>
      <h1 class="leader align-center">Which code is better?</h1>
    </header>
    <section class="main hbox space-between">
      <article>
        <pre class="language-java line-numbers" style="white-space: pre-wrap"><code class="language-java">private List&lt;Intent> buildInitialIntents(@NonNull Context context, @NonNull PackageManager pm, @NonNull Intent resolveIntent, @NonNull Intent emailIntent, @NonNull List&lt;Uri> attachments) {
  final List&lt;ResolveInfo> resolveInfoList = pm.queryIntentActivities(resolveIntent, PackageManager.MATCH_DEFAULT_ONLY);
  final List&lt;Intent> initialIntents = new ArrayList&lt;Intent>();
    for (ResolveInfo info : resolveInfoList) {
      final Intent packageSpecificIntent = new Intent(emailIntent);
      packageSpecificIntent.setPackage(info.activityInfo.packageName);
      grantPermission(context, emailIntent, info.activityInfo.packageName, attachments);
      if (packageSpecificIntent.resolveActivity(pm) != null) {
        initialIntents.add(packageSpecificIntent);
      }
    }
  return initialIntents;
}</code></pre>
      </article>
      <article>
        <pre class="language-java line-numbers" style="white-space: pre-wrap"><code class="language-java">private List&lt;Intent> buildInitialIntents(@NonNull PackageManager pm, @NonNull Intent resolveIntent, @NonNull Intent emailIntent) {
    final List&lt;ResolveInfo> resolveInfoList = pm.queryIntentActivities(resolveIntent, PackageManager.MATCH_DEFAULT_ONLY);
    final List&lt;Intent> initialIntents = new ArrayList&lt;Intent>();
    for (ResolveInfo info : resolveInfoList) {
        final Intent packageSpecificIntent = new Intent(emailIntent);
        packageSpecificIntent.setPackage(info.activityInfo.packageName);
        if (packageSpecificIntent.resolveActivity(pm) != null) {
            initialIntents.add(packageSpecificIntent);
        }
    }
    return initialIntents;
}</code></pre>
      </article>
    </section>
    <footer>
      <div class="grid">
        <div class="row cells3">
          <div class="cell align-center">
            <button class="button large-button success">
              <span class="mif-thumbs-up"></span>
              The left one is better
              <small>(&larr;)</small>
            </button>
          </div>
          <div class="cell align-center">
            <button class="button large-button warning">
              <span class="mif-question"></span>
              There is no difference in quality of them
              <small>(Esc)</small>
            </button>
          </div>
          <div class="cell align-center">
            <button class="button large-button success">
              <span class="mif-thumbs-up"></span>
              The right one is better
              <small>(&rarr;)</small>
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
  import Prism from 'prismjs'
  import 'prismjs/themes/prism-okaidia.css'
  import 'prismjs/components/prism-java.min.js'
  import 'prismjs/plugins/line-numbers/prism-line-numbers.js'
  import 'prismjs/plugins/line-numbers/prism-line-numbers.css'

  export default {
    name: 'HelloWorld',
    mounted() {
      Prism.highlightAll()
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
