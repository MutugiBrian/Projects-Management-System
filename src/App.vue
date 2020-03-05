
<template>
  <div id="app" style="background-color:#e9e9e9;">
    <div id="nav">

      <nav-bar></nav-bar>

    </div>

    <div class="container-fluid">
      <div class="col-md-12">
           <router-view></router-view>
      </div>
    </div>

    <footer class="footer fixed-bottom text-center bg-info pt-2 pb-2">
     <small class="text-white">&copy; 2019 ESS

     </small>


    </footer>

  </div>
</template>

<script>

import NavBar from './components/NavBar.vue'
export default {
  name: 'app',
  components: {
    NavBar
  },

  data() {
    return {
      menu: [],
    }
 },

 mounted() {
  
    this.$store.dispatch("addAllProjectData"); 
    this.$store.dispatch("addCountry"); 
    this.$store.dispatch("addRegion"); 
    this.$store.dispatch("addPartyTo"); 
    this.$store.dispatch("addCountryClassification"); 
    this.$store.dispatch("addProjectStatus"); 
    this.$store.dispatch("addCountryProjectCount"); 

 },

};

</script>

<style>
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
}
#nav {
  
}

#nav a {
  font-weight: bold;
  color: #ffffff;
}

#nav a.router-link-exact-active {
  color: #ffff9b;
}
</style>
