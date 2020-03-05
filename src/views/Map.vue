<template>
  <div xclass="map">
    
    <l-map style="height:700px;background-color: rgb(123, 173, 223);" :min-zoom=2 ref="map" :zoom=2 :center="[0.02, 36.9]"></l-map>

    <b-modal id="country-detail" size="lg">

        <template slot="modal-header" slot-scope="{ close }">
            <!-- <b-button class="close" @click="close()">
              ×
            </b-button> -->
            <table>
            <tr>
              <td><v-flag :country="iso2" size="normal" /> &nbsp; </td>
              <td><h5 class="modal-title" > {{modalTitle}}</h5></td>
            </tr>
            </table>
            <button type="button" class="close" @click="close()">
              ×
            </button>
        </template>
      
      <table class="popup table" v-for="p in projects.data">
      <tr>
        <td><strong>Title</strong></td>
        <td colspan="3">{{p.title}}</td>
      </tr>
      <tr>
        <td><strong>Summary</strong></td>
        <td  colspan="3">{{p.summary}}</td>
      </tr>
      <tr>
        <td><strong>Objectives</strong></td>
        <td  colspan="3">{{p.objectives}}</td>
      </tr>

       <tr>
        <td><strong>Party to</strong></td>
        <td  colspan="3">
           <template v-for="pt in p.party">
             {{pt.party_name}};
           </template>
        </td>
      </tr>
      <tr>
        <td style="width:110px;"><strong>Co-financing</strong></td>
        <td class="text-left">{{p.co_financing_usd | toCurrency}}</td>
        <td class="text-right"><strong>Project Fund</strong></td>
        <td style="width:100px;" class="text-left">{{p.sp_trust_fund_usd | toCurrency}}</td>
      </tr>
      <tr>
        <td><strong>Duration</strong></td>
        <td colspan="3">{{parseInt(p.duration_months)}} months</td>
      </tr>
      <tr v-if="p.story_url ">
        <td><strong>Story URL</strong></td>
        <td  colspan="3"><a :href="p.story_url" target="_blank">{{p.story_url}}</a></td>
      </tr>
      </table>
    </b-modal>

  </div>
  
</template>

<script>
import L from 'leaflet';
var esri = require('esri-leaflet');
import Event from "../event.js";

//import "../utils/leaflef-bubble-custom";
import "../utils/BubbleLayer";

import {
  LMap, LTileLayer, LMarker, LGeoJson, LPopup,
} from 'vue2-leaflet';

export default {
  name: 'DashboardMap',
  props: {


  },
  components: {
    LMap,
    LTileLayer,
    LMarker,
    LGeoJson,
    LPopup,
  },
  data() {
    return {

      modalTitle: '',
      iso2: '',

      projects: { 

        data: {},
        bubbles: {},
      
      },

    };
  },

  computed: {

  },

  watch: {

  },

  methods: {

   loadBaseMap() {
     
         const UNbaseMap = esri.tiledMapLayer({
            url: 'https://uneplivemapservices.unep.org/arcgis/rest/services/UNBASEMAP_Tiled/MapServer',
            maxZoom: 5,
            minZoom: 2
          });
        
         this.$refs.map.mapObject.addLayer(UNbaseMap);    
    },

    prepareProjectBubblesOnMap() {

        var vm            = this;
        var feature       = {};
        feature.type      = 'FeatureCollection';
        feature.features  = [];
        var country, tmp  = {};

        this.$_.forOwn( this.$store.getters.getCountryProjectCount , function( val, key ) { 
       
            tmp = {

                type: 'Feature',
                geometry: {type: 'Point',         
                  coordinates: [ parseFloat(val.lon), parseFloat(val.lat) ],
                },
                properties: {
                  Country: val.name,
                  Projects: parseInt(val.projects.count),
                  id: val.iso_2,
                  
                },
              
              };

            feature.features.push( tmp );
      
         });
       
       feature.features = this.$_.filter(feature.features, (f) => {
          if ( typeof f.properties != 'undefined' )
          {
             return f.properties.Projects > 0 ;
          }  
      });

      this.addProjectBubblesOnMap( feature );
      this.projects.data = feature ;
       
    },

    addProjectBubblesOnMap( feature ){

      if ( ! this.$_.isEmpty(this.projects.bubbles) )
      {
        this.$refs.map.mapObject.removeLayer( this.projects.bubbles );
      }

      this.projects.bubbles = L.bubbleLayer( feature , {
                property: "Projects",
                legend: false,
                max_radius : 15,
                //scale: true,
                tooltip : true
       });

       this.$refs.map.mapObject.addLayer( this.projects.bubbles );
        
    },


  },

  mounted() {
   
     this.loadBaseMap();
     this.prepareProjectBubblesOnMap();
     
     this.$store.watch(
        (state, getters) => getters.getCountryProjectCount,
        (newVal, oldVal) => {
            this.prepareProjectBubblesOnMap();
         },
       );


     Event.$on("bubble-layer-clicked", payload => {
        console.log( payload );

        var clickedCountryIso2 = payload.id ;
        
        
        this.$bvModal.show('country-detail') ;

        var allProjects = this.$store.getters.getAllProjectData ;

        var countryProjects = this.$_.filter(allProjects, { country: [ { country_iso_2: clickedCountryIso2 } ]});

        //console.log( countryProjects );

        this.projects.data = countryProjects ;

        var countries = this.$store.getters.getCountry ;

        var activeCountry = this.$_.filter( countries , { iso_2: clickedCountryIso2 });

        //console.log( activeCountry );

        this.modalTitle = 'Project in ' + activeCountry[0].name ;
        this.iso2       =  activeCountry[0].iso_2 ;



     });
    

  },

  created() {

    //alert( L.bubbleLayer );

  },

};
</script>

<style>
.card-body {
  padding: 0;
}
.card-header {
  padding: 0.5rem 1.25rem;
  }

.table.popup td {
  font-size:0.85em;
}

</style>
