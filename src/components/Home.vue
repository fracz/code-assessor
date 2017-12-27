<template>
  <div class="home">
    <div class="left-bg"></div>
    <div class="right-bg"></div>
    <div class="content">
      <h1>Which code is better?</h1>
      <h2>Tell us your opinion and help in teaching AI what <em>good code</em> means!</h2>
      <router-link to="/play" class="play">Show me the code!</router-link>
      <p>
        <strong>What to expect?</strong>
        We will show you a Java source code refactoring changes and ask you to tell if the change does
        or does not improve the overall quality of the code. Every change is a single method no longer than 100 lines.
      </p>
      <p>
        <strong>But it is strongy opinion based what <em>good code</em> means!</strong>
        Exactly! But maybe, maybe there are some rules we can discover thanks to your answers!
      </p>
      <p>
        <strong>Time limits?</strong>
        30 seconds per every change. If you can't distinguish which code is better after this time, you will be
        presented with another one. You can stop the whole experiment when you want and come back later.
      </p>
      <p>
        <strong>We trust YOU!</strong>
        Do not start if you are tired or not willing to analyze any source code at the moment. Do not start if you are
        not
        a programmer. There are no good or bad answers. There are no rewards besides our gratitude.
      </p>
      <p>
        <strong>Want to know more, suggest something or follow the project afterwards?</strong>
        Check out the project that we work on on <a
          href="https://www.researchgate.net/project/Discover-what-good-code-really-means">ResearchGate</a>.
      </p>
    </div>
  </div>
</template>

<script>
  export default {}
</script>

<style scoped>
  .home {
    color: #eee;
    margin: 0;
    padding: 0;
    background: #111;
    height: 100%;
    width: 100%;
    overflow-y: auto;
  }

  .content {
    margin: 0 auto;
    width: 50%;
    text-align: center;
  }

  h1 {
    margin: 0;
    padding: 30px 0 10px;
    font-family: Consolas, monospace;
    text-align: center;
    font-size: 5em;
    font-weight: bold;
  }

  h2 {
    text-align: center;
  }

  p {
    font-size: 1.3em;
    max-width: 80%;
    margin: 0 auto;
    margin-bottom: 15px;
  }

  strong {
    display: block;
    margin-bottom: 5px;
  }

  .left-bg {
    position: absolute;
    height: 100%;
    width: 600px;
    max-width: 25%;
    background: url('../assets/left.png') no-repeat center center;
    background-size: cover;
  }

  .right-bg {
    position: absolute;
    right: 0;
    height: 100%;
    width: 600px;
    max-width: 25%;
    background: url('../assets/right.png') no-repeat center center;
    background-size: cover;
  }

  a {
    color: orange;
  }

  a.play {
    margin: 35px 0;
    font-size: 3em;
    border: 3px solid orange;
    display: inline-block;
    padding: 10px 15px;
    font-family: Consolas, monospace;
    text-decoration: none;
  }

  a.play:hover {
    color: darkorange;
    border-color: darkorange;
  }
</style>
