<template>
  <div>
    <h1 class="align-center">What is your experience in coding?</h1>
    <p class="align-center">
      This information will help us in determining the respondents' profile.<br>
      This answer is the only information we store about you.
    </p>
    <form @submit.prevent="begin()">
      <label class="input-control radio">
        <input type="radio" value="-2" v-model="experience">
        <span class="check"></span>
        <span class="caption">I'm not a coder!</span>
      </label>
      <label class="input-control radio">
        <input type="radio" value="0" v-model="experience">
        <span class="check"></span>
        <span class="caption">Less than 1 year</span>
      </label>
      <label class="input-control radio">
        <input type="radio" value="1" v-model="experience">
        <span class="check"></span>
        <span class="caption">1-2 years</span>
      </label>
      <label class="input-control radio">
        <input type="radio" value="2" v-model="experience">
        <span class="check"></span>
        <span class="caption">2-3 years</span>
      </label>
      <label class="input-control radio">
        <input type="radio" value="3" v-model="experience">
        <span class="check"></span>
        <span class="caption">3-5 years</span>
      </label>
      <label class="input-control radio">
        <input type="radio" value="4" v-model="experience">
        <span class="check"></span>
        <span class="caption">5-10 years</span>
      </label>
      <label class="input-control radio">
        <input type="radio" value="5" v-model="experience">
        <span class="check"></span>
        <span class="caption">over 10 years</span>
      </label>
      <label class="input-control radio">
        <input type="radio" value="-1" v-model="experience">
        <span class="check"></span>
        <span class="caption">I'd rather not say</span>
      </label>
      <div class="align-center">
        <button class="button primary large-button" :disabled="experience !== undefined">Begin!</button>
      </div>
    </form>
  </div>
</template>

<script>
  export default {
    data() {
      return {
        experience: undefined
      }
    },
    methods: {
      begin() {
        if (this.experience !== undefined) {
          if (this.experience < -1) {
            window.location.assign('https://www.google.com/doodles/celebrating-50-years-of-kids-coding');
          } else {
            this.$http.post('respondents', {experience: this.experience}).then(response => {
              const respondent = response.body;
              this.$localStorage.set('respondentId', respondent.id);
              this.$router.push('/play');
            });
          }
        }
      }
    }
  }
</script>

<style scoped>
  .input-control {
    display: block;
    clear: both;
    margin: 0 auto;
    width: 200px;
  }
</style>
