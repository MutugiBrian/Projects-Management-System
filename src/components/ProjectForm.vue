<template>
  <div>

     <b-form-group label-cols="3" label-cols-lg="1" label-size="sm" label="Title" label-for="project-title">
         <b-form-input id="project-title" v-model="project.title" size="sm"></b-form-input>
     </b-form-group>

     <b-form-group label-cols="3" label-cols-lg="1" label-size="sm" label="Summary" label-for="project-summary">
         <b-form-textarea
            id="project-summary"
            v-model="project.summary"
            placeholder="Enter project's summary"
            rows="3"
            max-rows="4"
          ></b-form-textarea>
     </b-form-group>

     <b-form-group label-cols="3" label-cols-lg="1" label-size="sm" label="Objectives" label-for="project-objectives">
         <b-form-textarea
            id="project-objectives"
            v-model="project.objective"
            placeholder="Enter project's objectives"
            rows="3"
            max-rows="4"
          ></b-form-textarea>
     </b-form-group>

     <div class="row">
     
         <div class="col-sm-6">
             <div class="form-group row"> <label for="country" class="col-md-2 control-label col-form-label-sm" label-size="sm">Country</label>           
                <div class="col-md-10"><v-select id="country" v-model="project.country_iso_2" label="name" :options="selectOptions.country" :reduce="name => name.iso_2"  multiple /> </div>
             </div>
         </div>

        <div class="col-sm-6">
           <div class="form-group row"> <label for="country_classification_id" class="col-md-2 control-label col-form-label-sm" label-size="sm">Classification</label>
              <div class="col-md-10"><v-select id="country_classification_id" v-model="project.country_classification_id" label="classification" :options="selectOptions.countryClassification" :reduce="classification => classification.id" /> </div>
           </div>
        </div>

     </div>

     <div class="row">
     
         <div class="col-sm-6">
             <div class="form-group row"> <label for="region" class="col-md-2 control-label col-form-label-sm" label-size="sm">Region</label>           
                <div class="col-md-10"><v-select id="region" v-model="project.region_id" label="region_name" :options="selectOptions.region" :reduce="region_name => region_name.id" /> </div>
             </div>
         </div>

        <div class="col-sm-6">
           <div class="form-group row"> <label for="party_id" class="col-md-2 control-label col-form-label-sm" label-size="sm">Party To</label>
              <div class="col-md-10"><v-select id="party_id" v-model="project.party_id" label="party" :options="selectOptions.partyTo" :reduce="party => party.id" multiple /> </div>
           </div>
        </div>

     </div>

     <div class="row">
     
         <div class="col-sm-6">
              <div class="form-group row"> <label for="sp_trust_fund_usd" class="col-md-3 control-label col-form-label-sm" label-size="sm">Programme Fund</label>           
                <div class="col-md-9"> 
                 
                 <b-input-group size="sm" prepend="US $">
                   <b-form-input id="sp_trust_fund_usd" v-model="project.sp_trust_fund_usd" size="sm"></b-form-input> 
                 </b-input-group>   
                </div>
             </div> 

         </div>

        <div class="col-sm-6">
           <div class="form-group row"> <label for="co_financing_usd" class="col-md-3 control-label col-form-label-sm" label-size="sm">Co-financing</label>
               <div class="col-md-9"> 

               <b-input-group size="sm" prepend="US $">
                  <b-form-input id="co_financing_usd" v-model="project.co_financing_usd" size="sm"></b-form-input>  
               </b-input-group>

               </div> 
           </div>
        </div>

     </div>

     <div class="row">
     
         <div class="col-sm-6">

              <div class="form-group row"> <label for="status_id" class="col-md-3 control-label col-form-label-sm" label-size="sm">Status</label>           
                <div class="col-md-9"> <v-select id="status_id" v-model="project.status_id" label="status" :options="selectOptions.projectStatus" :reduce="status => status.id" />  </div>
             </div> 

         </div>

        <div class="col-sm-6">
           <div class="form-group row"> <label for="duration_months" class="col-md-3 control-label col-form-label-sm" label-size="sm">Duration&nbsp;(months)</label>
               <div class="col-md-9"> <b-form-input id="duration_months" placeholder="Enter duration in months" v-model="project.duration_months" size="sm"></b-form-input>  </div> 
           </div>
        </div>

     </div>


     <b-form-group label-cols="3" label-cols-lg="1" label-size="sm" label="Story URL" label-for="project-story-url">
         <b-form-input id="project-story-url" v-model="project.story_url" size="sm"></b-form-input>
     </b-form-group>

     <b-form-group label-cols="3" label-cols-lg="1" label-size="sm" label="Kweywords" label-for="project-title">
         <b-form-input id="project-title" v-model="project.keyword" size="sm"></b-form-input>
     </b-form-group>


     <div class="row"> 
          <div class="col-md-12 text-right">
             <b-button size="sm" @click="addEditProject" variant="primary"> Update </b-button>
          </div>
     </div>



  </div>
</template>

<script>

export default {
  name: 'ProjectForm',
  props: {
    
  },

  components: {
    
  },

  data () {
    return {

      project: {

        mode: 'add',
        id: null,
        title: '',
        summary: '',
        objectives: '',
        country_classification_id: '',
        sp_trust_fund_usd: '',
        co_financing_usd: '',
        duration_months: '',
        status_id: '',
        story_url: '',
        country_iso_2: [],
        region_id: [],
        party_id: [],
        keyword:'',
      
      },

      selectOptions: {
        
          country: [],
          region:  [],
          partyTo:  [],
          countryClassification: [],
          projectStatus:[],
      
      },

    }
  },

  methods: {

     updateCountry: function () {
         this.selectOptions.country = this.$store.getters.getCountry;
     },
     updateRegion: function () {
         this.selectOptions.region  = this.$store.getters.getRegion;
     },
     updatePartyTo: function () {
         this.selectOptions.partyTo  = this.$store.getters.getPartyTo;
     },
     updateCountryClassification: function () {
         this.selectOptions.countryClassification  = this.$store.getters.getCountryClassification;
     },
     updateProjectStatus: function () {
         this.selectOptions.projectStatus  = this.$store.getters.getProjectStatus;
     },

     addEditProject: function () {

       if (this.project.mode == 'add' )
       {
           this.$axios.post( process.env.VUE_APP_API_PATH + 'project', { params: this.project } )
                .then(res => {             
                
           });
       
       } else if (this.project.mode == 'edit')
       {
 
           this.$axios.put( process.env.VUE_APP_API_PATH + 'project/' + this.project.id , { params: this.project } )
                .then(res => {             
                
           });
       
       }
         
   

     },

  },

  mounted() {

      this.updateCountry();
      this.updateRegion();
      this.updatePartyTo();
      this.updateCountryClassification();
      this.updateProjectStatus();

      this.$store.watch(
        (state, getters) => getters.getCountry,
        (newVal, oldVal) => {
            this.updateCountry();
         },
       );
      this.$store.watch(
        (state, getters) => getters.getRegion,
        (newVal, oldVal) => {
            this.updateRegion();
         },
       );
      this.$store.watch(
        (state, getters) => getters.getPartyTo,
        (newVal, oldVal) => {
            this.updatePartyTo();
         },
       );
       this.$store.watch(
        (state, getters) => getters.getCountryClassification,
        (newVal, oldVal) => {
            this.updateCountryClassification();
         },
       ); 
       this.$store.watch(
        (state, getters) => getters.getProjectStatus,
        (newVal, oldVal) => {
            this.updateProjectStatus();
         },
       ); 

         

  },

  watch : {


  }

}
</script>

<style>


</style>
