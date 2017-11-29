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
            <button class="button large-button danger">
              <span class="mif-thumbs-down"></span>
              No, it makes the code worse
              <small>(&darr;)</small>
            </button>
          </div>
          <div class="cell align-center">
            <button class="button large-button warning">
              <span class="mif-question"></span>
              This change does not change the quality of the code
              <small>(Esc)</small>
            </button>
          </div>
          <div class="cell align-center">
            <button class="button large-button success">
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
        unifiedDiff: 'aa',
        sideBySideDiff: 'aa',
        diffStyle: 'side-by-side'
      }
    },
    mounted() {

      const code = `diff --git a/6e8401e88ece0481cf9102c0aec6304ec5194b85.java b/5214a611412dd27bf32ba827cf47d0f0d2db239e.java
index 6e8401e..5214a61 100644
--- a/6e8401e88ece0481cf9102c0aec6304ec5194b85
+++ b/5214a611412dd27bf32ba827cf47d0f0d2db239e
@@ -1,66 +1,49 @@
 @Override
 public void send(@NonNull Context context, @NonNull CrashReportData errorContent) throws ReportSenderException {
     final PackageManager pm = context.getPackageManager();
     final String subject = context.getPackageName() + " Crash Report";
     final String body = buildBody(errorContent);
     final InstanceCreator instanceCreator = new InstanceCreator();
     final ArrayList<Uri> attachments = instanceCreator.create(config.attachmentUriProvider(), new DefaultAttachmentProvider()).getAttachments(context, config);
     final Intent resolveIntent = new Intent(android.content.Intent.ACTION_SENDTO);
     resolveIntent.setData(Uri.fromParts("mailto", config.mailTo(), null));
     resolveIntent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
     resolveIntent.putExtra(android.content.Intent.EXTRA_SUBJECT, subject);
     resolveIntent.putExtra(android.content.Intent.EXTRA_TEXT, body);
     final ComponentName componentName = resolveIntent.resolveActivity(pm);
     if (componentName != null) {
         if (attachments.size() == 0) {
             context.startActivity(resolveIntent);
         } else {
             String packageName = componentName.getPackageName();
             final Intent emailIntent = new Intent(attachments.size() == 1 ? Intent.ACTION_SEND : Intent.ACTION_SEND_MULTIPLE);
             emailIntent.putExtra(Intent.EXTRA_EMAIL, new String[] { config.mailTo() });
             emailIntent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
             emailIntent.putExtra(Intent.EXTRA_SUBJECT, subject);
             emailIntent.putExtra(Intent.EXTRA_TEXT, body);
             emailIntent.setType("message/rfc822");
             emailIntent.putParcelableArrayListExtra(Intent.EXTRA_STREAM, attachments);
             if (packageName.equals("android")) {
-                final List<ResolveInfo> resolveInfoList = pm.queryIntentActivities(resolveIntent, PackageManager.MATCH_DEFAULT_ONLY);
-                final List<Intent> initialIntents = new ArrayList<Intent>();
-                for (ResolveInfo info : resolveInfoList) {
-                    final Intent packageSpecificIntent = new Intent(emailIntent);
-                    packageSpecificIntent.setPackage(info.activityInfo.packageName);
-                    if (packageSpecificIntent.resolveActivity(pm) != null) {
-                        initialIntents.add(packageSpecificIntent);
-                    }
-                }
+                // multiple activities support the intent and no default is set
+                final List<Intent> initialIntents = buildInitialIntents(pm, resolveIntent, emailIntent);
                 if (initialIntents.size() > 1) {
-                    final Intent chooser = new Intent(Intent.ACTION_CHOOSER);
-                    chooser.putExtra(Intent.EXTRA_INTENT, initialIntents.remove(0));
-                    chooser.putExtra(Intent.EXTRA_INITIAL_INTENTS, initialIntents.toArray(new Intent[initialIntents.size()]));
-                    chooser.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
-                    context.startActivity(chooser);
+                    showChooser(context, initialIntents);
                     return;
                 } else if (initialIntents.size() == 1) {
+                    // only one of them supports attachments, use that one
                     packageName = initialIntents.get(0).getPackage();
                 }
-                emailIntent.setPackage(packageName);
-                if (emailIntent.resolveActivity(pm) != null) {
-                    if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
-                        emailIntent.addFlags(Intent.FLAG_GRANT_READ_URI_PERMISSION);
-                    } else {
-                        // flags do not work on extras prior to android 5, so we have to grant read permissions manually
-                        for (Uri uri : attachments) {
-                            context.grantUriPermission(packageName, uri, Intent.FLAG_GRANT_READ_URI_PERMISSION);
-                        }
-                    }
-                    context.startActivity(emailIntent);
-                } else {
-                    ACRA.log.w(LOG_TAG, "No email client supporting attachments found. Attachments will be ignored");
-                    context.startActivity(resolveIntent);
-                }
+            }
+            emailIntent.setPackage(packageName);
+            if (emailIntent.resolveActivity(pm) != null) {
+                grantPermission(context, emailIntent, packageName, attachments);
+                context.startActivity(emailIntent);
+            } else {
+                ACRA.log.w(LOG_TAG, "No email client supporting attachments found. Attachments will be ignored");
+                context.startActivity(resolveIntent);
             }
         }
     } else {
         throw new ReportSenderException("No email client found");
     }
 }
\\ No newline at end of file
`;

      const diff = new Diff2HtmlUI({diff: code});

      diff.draw('#unified', {});
      diff.draw('#sideBySide', {inputFormat: 'json', outputFormat: 'side-by-side', matching: 'lines', synchronisedScroll: true});
      diff.highlightCode('#unified');
      diff.highlightCode('#sideBySide');

//      this.unifiedDiff = Diff2Html.getPrettyHtml(code);
//      this.sideBySideDiff = Diff2Html.getPrettyHtml(code, {
//          outputFormat: 'side-by-side'
//        }
//      );
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
