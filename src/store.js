import Vue from 'vue';
import Vuex from 'vuex';
import axios from 'axios';
Vue.use(Vuex)

export default new Vuex.Store({
  state: {
     allProjectData: {},
     country: {},
     region:{},
     partyTo:{},
     countryClassification: {},
     projectStatus: {},
     countryProjectCount: {},

  },

  getters: {
  
     getAllProjectData: state => {
        return state.allProjectData;
     },

     getCountry: state => {
        return state.country;
     },

     getRegion: state => {
        return state.region;
     },

     getPartyTo: state => {
        return state.partyTo;
     },     

     getCountryClassification: state => {
        return state.countryClassification;
     },   

     getProjectStatus: state => {
        return state.projectStatus;
     },   

     getCountryProjectCount: state => {
        return state.countryProjectCount;
     }, 

  },

  mutations: {

    ADD_ALL_PROJECT_DATA: (state, payload) => {
       state.allProjectData = payload;
    },

    ADD_COUNTRY: (state, payload) => {
       state.country = payload;
    },

    ADD_REGION: (state, payload) => {
       state.region = payload;
    },

    ADD_PARTY_TO: (state, payload) => {
       state.partyTo = payload;
    },    

    ADD_COUNTRY_CLASSIFICATION: (state, payload) => {
       state.countryClassification = payload;
    },  

    ADD_PROJECT_STATUS: (state, payload) => {
       state.projectStatus = payload;
    }, 
      
    ADD_COUNTRY_PROJECT_COUNT: (state, payload) => {
       state.countryProjectCount = payload;
    }, 

  },

  actions: {

    addAllProjectData: (state) => {
       axios.get( process.env.VUE_APP_API_PATH + 'project' )
            .then(res => {             
            state.commit("ADD_ALL_PROJECT_DATA", res.data.data );
       }); 
    },

    addCountry: (state) => {
       axios.get( process.env.VUE_APP_API_PATH + 'country' )
            .then(res => {             
            state.commit("ADD_COUNTRY", res.data.data );
       }); 
    },

    addRegion: (state) => {
       axios.get( process.env.VUE_APP_API_PATH + 'region' )
            .then(res => {             
            state.commit("ADD_REGION", res.data.data );
       }); 
    },

    addPartyTo: (state) => {
       axios.get( process.env.VUE_APP_API_PATH + 'party-to' )
            .then(res => {             
            state.commit("ADD_PARTY_TO", res.data.data );
       }); 
    },

    addCountryClassification: (state) => {
       axios.get( process.env.VUE_APP_API_PATH + 'countryclass' )
            .then(res => {             
            state.commit("ADD_COUNTRY_CLASSIFICATION", res.data.data );
       }); 
    },

    addProjectStatus: (state) => {
       axios.get( process.env.VUE_APP_API_PATH + 'projectstatus' )
            .then(res => {             
            state.commit("ADD_PROJECT_STATUS", res.data.data );
       }); 
    },

    addCountryProjectCount: (state) => {
       axios.get( process.env.VUE_APP_API_PATH + 'country_project' )
            .then(res => {             
            state.commit("ADD_COUNTRY_PROJECT_COUNT", res.data.data );
       }); 
    },

  }

});
