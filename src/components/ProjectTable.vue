<template>
  <div>

     <b-modal id="project-detail" size="lg">

       <template slot="modal-header" slot-scope="{ close }">
            <!-- <b-button class="close" @click="close()">
              ×
            </b-button> -->
            <table>
            <tr>
              <td><v-flag :country="iso2" size="big" /> &nbsp; </td>
              <td style="float:left"><h5 class="modal-title" > {{modalTitle}}</h5></td>
            </tr>
            </table>
            <button type="button" class="close" @click="close()">
              ×
            </button>
        </template>
      
      <table class="popup table" v-for="p in project.data">
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
        <td  colspan="3"  v-html="listObjectives(p.objectives)">
        </td>
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
        <td class="text-left">{{p.co_financing_usd | toCurrency }}</td>
        <td class="text-right"><strong>Programme Fund</strong></td>
        <td style="width:100px;" class="text-left">{{p.sp_trust_fund_usd | toCurrency }}</td>
      </tr>
      <tr>
        <td><strong>Duration</strong></td>
        <td  colspan="3">{{parseInt(p.duration_months)}} months</td>
      </tr>
      <tr v-if="p.story_url ">
        <td><strong>Story URL</strong></td>
        <td  colspan="3"><a :href="p.story_url" target="_blank">{{p.story_url}}</a></td>
      </tr>
      </table>

    </b-modal>


     <vue-good-table v-if="this.project.rows.length > 0 "
      :columns="project.columns"
      :rows="project.rows"
      :line-numbers="true"
      :pagination-options="project.pagination"
      styleClass="vgt-table condensed bordered "
      :fixed-header="false"
      :search-options="{ enabled: true }"
      :sort-options="{
      enabled: true,
      }"
      >

      <!-- <div slot="table-actions">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <b-button variant="success" @click="showProjectForm" size="sm">New project</b-button>
        &nbsp;&nbsp;
        <b-button variant="info" size="sm">Refresh</b-button>&nbsp;&nbsp;
      </div> -->

      <template slot="table-row" slot-scope="props">

        <span v-if="props.column.field == 'title' ">
          <span>
            <b-link class="title-link" href="#" @click="projectClicked( props.row.id )">{{props.row.title }}</b-link>
          </span>
        </span>

        <span v-else-if="props.column.field == 'sp_trust_fund_usd' ">
          <span>
             {{props.row.sp_trust_fund_usd | toCurrency }}
          </span>
        </span>

        <span v-else-if="props.column.field == 'co_financing_usd' ">
          <span>
             {{props.row.co_financing_usd | toCurrency }}
          </span>
        </span>



      </template>

   </vue-good-table>

   <b-modal id="project-form" title="Project's Form" size="xl">
    
       <ProjectForm />
        
   </b-modal>

  </div>
</template>

<script>

import ProjectForm from '@/components/ProjectForm.vue'

export default {
  name: 'ProjectTable',
  props: {
    
  },

  components: {
    ProjectForm,
  },

  data () {
    return {

      modalTitle: '',

      project: {

         data: {},

         
         clicked: {},
      
         rows: [],
         columns: [
           {
              label: 'Title',
              field: 'title',
           }, 
           {
              label: 'Country',
              field: 'country',
              type: 'String',
              formatFn: this.formatProjectCountry,
              sortFn: this.sortProjectCountry
           },
           {
              label: 'Region',
              field: 'regions',
              type: 'String',
              formatFn: this.formatProjectRegion,
              sortFn: this.sortProjectRegion
           },
           {
              label: 'Party',
              field: 'party',
              type: 'String',
              formatFn: this.formatProjectParty,
              sortFn: this.sortProjectParty,
              sortable: false,
           },
           {
              label: 'Trust Fund',
              field: 'sp_trust_fund_usd',
              type: 'decimal',
           }, 
           {
              label: 'Co Financing',
              field: 'co_financing_usd',
              type: 'decimal',
           },            {
              label: 'Duration (months)',
              field: 'duration_months',
              type: 'number',
              formatFn: this.formatDurationMonth,
           }, 
         
         ],
         pagination: {
          enabled: true,
          mode: 'records',
          perPage: 10,
          position: 'top',
          perPageDropdown: [10, 20, 50],
          dropdownAllowAll: true,
          setCurrentPage: 1,
          nextLabel: 'next',
          prevLabel: 'prev',
          rowsPerPageLabel: 'Rows per page',
          ofLabel: 'of',
          pageLabel: 'page', // for 'pages' mode
          allLabel: 'All',
        },
      
      },

    }
  },

  methods: {
    listObjectives: function(obs){
          var lobs = obs.replace(/\.+/g,'.|').replace(/\?/g,'?|').replace(/\!/g,'!|').split("|");
          var ltop = lobs.toString().substring(0, lobs.toString().indexOf(':'));
          var lbot = lobs.toString().split(':').pop();
          var litems = lbot.toString().replace(/;/gi,'</li><li>');
          lobs = ltop +' : <ol><li>' + litems + '</li></ol>';
          return lobs;
    
    },
    projectClicked: function (id) {
    
      this.$bvModal.show('project-detail') ;

      var allProjects = this.$store.getters.getAllProjectData ;

      var countryProjects = this.$_.filter(allProjects, { id: id });

      this.project.data = countryProjects ;
      this.modalTitle = countryProjects[0].title;
      this.iso2       = countryProjects[0].country[0].country_iso_2;

    },

    showProjectForm: function () {

        this.$bvModal.show('project-form') ;

    },

    formatDurationMonth: (v) => {
      return parseInt(v);
    },

    formatProjectParty: v => {
      
       var p = [];

       v.forEach(function (val, idx) {
          p.push( val.party_name );
       });

       //return p.toString(' ') ;
       return p.join(', ') ;

    },

    formatProjectCountry: v => {
      // return ''+ v[0].country_name;
      if(typeof v[0] == 'undefined'){
      return 'no country set';
      }else{
       return ''+ v[0].country_name;
      }
    },

    formatProjectRegion: v => {
       //return ''+ v[0].region_name;
       if(typeof v[0] == 'undefined'){
      return 'no region set';
      }else{
        var ts = v[0].region_name;

        ts = ts.toString();

        return ts;
      }
    },

    sortProjectParty: (x, y, col, rowX, rowY) => {
         var px = [];
         x.forEach(function (val, idx) {
            px.push( val.party_name );
         });
         x = px.toString() ;
  
         var py = [];
         y.forEach(function (val, idx) {
            py.push( val.party_name );
         });
         y = py.toString() ;

        return (x < y ? -1 : (x > y ? 1 : 0));
    },

    sortProjectCountry: (x, y, col, rowX, rowY) => {
        console.log(x[0].country_name);
        x = x[0].country_name;
        y = y[0].country_name;
        return (x < y ? -1 : (x > y ? 1 : 0));
    },

    sortProjectRegion: (x, y, col, rowX, rowY) => {

         
         var testx = x[0].region_name;
         var testy = x[0].region_name;


         x= testx.toString();
         y= testy.toString();

        //return 0;
         
          
         //x = String(x[0].region_name);
         //console.log(x);
         //y = String(y[0].region_name);
         return (x < y ? -1 : (x > y ? 1 : 0));



            //  x = x[0].region_name ? ' ' + x[0].region_name : ' ' ;
            //  y = y[0].region_name ? ' ' + y[0].region_name : ' ' ;

            //  return (x < y ? -1 : (x > y ? 1 : 0));
    },

    updateProjectData: function () {

      this.project.rows = this.$store.getters.getAllProjectData ;
    
    },

  },

  mounted() {

     this.updateProjectData();

     this.$store.watch(
      (state, getters) => getters.getAllProjectData,
      (newVal, oldVal) => {

          this.updateProjectData();

       },
     );

    

  },

  watch : {


  },


}
</script>

<style>

table.vgt-table span {
  font-size:0.9em;
}
.table.popup td {
  font-size:0.85em;
}
table.vgt-table th {
  padding: .3em 1.5em .3em .75em
}
table.vgt-table.condensed td {
  padding: .15em 1.5em;
}

a.title-link {
  color:#258ec7;
}
table.vgt-table span {
font-size:14.4px !important;
}
.title-link{
font-size:14.4px !important;
}
</style>
