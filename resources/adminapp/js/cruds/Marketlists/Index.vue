<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header card-header-primary card-header-icon">
            <div class="card-icon">
              <i class="material-icons">assignment</i>
            </div>
            <h4 class="card-title">
              <strong>{{ $t('cruds.marketlist.title') }}</strong>
            </h4>
          </div>
          <div class="card-body">
            <router-link
              class="btn btn-primary"
              v-if="$can(xprops.permission_prefix + 'create')"
              :to="{ name: xprops.route + '.create' }"
            >
              <i class="material-icons">
                add
              </i>
              {{ $t('global.add') }}
            </router-link>
            <button
              type="button"
              class="btn btn-default"
              @click="fetchIndexData"
              :disabled="loading"
              :class="{ disabled: loading }"
            >
              <i class="material-icons" :class="{ 'fa-spin': loading }">
                refresh
              </i>
              {{ $t('global.refresh') }}
            </button>
            <b-button v-if="$can('export')" class="btn btn-primary" v-b-modal="'modal-1'" style="background: #f2a8ff; margin-left: 20px"> Export</b-button>
            <button v-if="$can('export')" class="btn btn-success" @click="logCheckedValues">Export Raw</button>
            <b-button class="btn btn-info" v-b-modal="'modal-3'" style="background: #f2a8ff; margin-left: 20px"> Export</b-button>
          </div>
          <div v-if="$can('export')" class="card-body row">
            <b-modal id="modal-1" title="Export Data">
              <div class="modal-body">
                <!-- Date inputs for start and end date selection -->
                <label for="start-date">Pilih tanggal :</label>
                <input type="date" id="start-date" v-model="startDate" @change="filterData" />
              </div>
              <div class="modal-body">
                <export-excel :data="filteredData" :name="customFilename">
                  <button class="btn btn-success">Export</button>
                </export-excel>
              </div>
            </b-modal>
          </div>
            <b-modal id="modal-2" ref="modal-2" title="Export Data 2">
              <div class="modal-body">
                <button class="btn btn-success"><export-excel :data="rawData">
                    Download
                </export-excel></button>
              </div>
            </b-modal>
            <b-modal id="modal-3" ref="modal-3" title="Cetak PDF">
              <div class="modal-body">
                <form method="GET" action="../../marketlist-report" target="_blank">
                  <div class="row" style="padding: 10px">
                    <div class="col-lg-3"> BU : </div>
                    <div class="col-lg-9">
                    <v-select
                      name="bu"
                      label="name"
                      :key="'bu-field'"
                      :value="report_query.bu_id"
                      :options="lists.bu"
                      :reduce="bu => bu.id"
                      @input="updateBu"
                    />
                    </div>
                  </div>
                  <div class="row" style="padding: 10px">
                    <div class="col-lg-3"> Site : </div>
                    <div class="col-lg-9">
                    <v-select
                      name="site"
                      label="name"
                      :key="'site-field'"
                      :value="report_query.site_id"
                      :options="lists.site"
                      :reduce="site => site.id"
                      @input="updateSite"
                    />
                    </div>
                  </div>
                  <div class="row" style="padding: 10px">
                    <div class="col-lg-3">
                      Dari : 
                    </div>
                    <div class="col-lg-6">
                        <vuejs-datepicker
                          input-class="form-control"
                          format="dd-MM-yyyy"
                          :value="report_query.startDate"
                          @input="updateStartDate"
                        >
                        </vuejs-datepicker>
                    </div>
                  </div>
                  <div class="row" style="padding: 10px">
                    <div class="col-lg-3">
                      Sampai : 
                    </div>
                    <div class="col-lg-6">
                        <vuejs-datepicker
                          input-class="form-control"
                          format="dd-MM-yyyy"
                          :value="report_query.endDate"
                          @input="updateEndDate"
                        >
                        </vuejs-datepicker>
                    </div>
                  </div>
                  <input type="hidden" name="bu_id" :value="report_query.bu_id">
                  <input type="hidden" name="site_id" :value="report_query.site_id">
                  <input type="hidden" name="startDate" :value="formatDate(report_query.startDate)">
                  <input type="hidden" name="endDate" :value="formatDate(report_query.endDate)">
                  <div class="row" style="padding: 10px">
                    <button type="submit" class="btn btn-sm btn-primary">Cetak PDF</button>
                  </div>
                    
                </form>
                  
              </div>
              
            </b-modal>
          <div class="card-body">
            <div class="row">
              <div class="col-md-12">
                <div class="table-overlay" v-show="loading">
                  <div class="table-overlay-container">
                    <material-spinner></material-spinner>
                    <span>Loading...</span>
                  </div>
                </div>
                <datatable
                  :columns="columns"
                  :data="data"
                  :total="total"
                  :query="query"
                  :xprops="xprops"
                  :HeaderSettings="false"
                  :pageSizeOptions="[10, 25, 50, 100]"
                >
                  <global-search :query="query" class="pull-left" />
                  <header-settings :columns="columns" class="pull-right" />
                        <template slot="DatatableCheckbox" slot-scope="props">
                          <DatatableCheckbox :value="props.row.selected" @change="handleCheckboxChange(props.row)" />
                        </template>
                </datatable>
              </div>
            </div>
          </div>
          
        </div>
      </div>
    </div>
  </div>
</template>

<style>
  /* Hide the default modal footer */
  .modal-footer {
    display: none;
  }
</style>

<script>
import { mapGetters, mapActions } from 'vuex'
import MarketlistActions from '@components/Datatables/MarketlistActions'
import TranslatedHeader from '@components/Datatables/TranslatedHeader'
import HeaderSettings from '@components/Datatables/HeaderSettings'
import GlobalSearch from '@components/Datatables/GlobalSearch'
import DatatableSingle from '@components/Datatables/DatatableSingle'
import DatatableEnum from '@components/Datatables/DatatableEnum'
import DatatableCheckbox from '../../components/Datatables/DatatableCheckbox.vue'

export default {
  components: {
    GlobalSearch,
    HeaderSettings
  },
  data() {
    return {
      customFilename: 'my_custom_filename.xlsx',
      startDate: '',
      endDate: '',
      filteredData: [],
      rawData: [],
      columns: [
        {
          title: '',
          field: 'selected',
          thComp: '',
          tdComp: DatatableCheckbox,
          sortable: false
        },
        {
          title: 'cruds.marketlist.fields.code',
          field: 'code',
          thComp: TranslatedHeader,
          sortable: true
        },
        {
          title: 'cruds.marketlist.fields.event',
          field: 'event',
          thComp: TranslatedHeader,
          sortable: true
        },
        {
          title: 'cruds.marketlist.fields.created_at',
          field: 'created_at',
          thComp: TranslatedHeader,
          sortable: true
        },
        {
          title: 'cruds.marketlist.fields.user',
          field: 'user.name',
          thComp: TranslatedHeader,
          tdComp: DatatableSingle,
          sortable: true
        },
        {
          title: 'cruds.marketlist.fields.bu',
          field: 'bu.name',
          thComp: TranslatedHeader,
          tdComp: DatatableSingle,
          sortable: true
        },
        {
          title: 'cruds.marketlist.fields.site',
          field: 'site.name',
          thComp: TranslatedHeader,
          tdComp: DatatableSingle,
          sortable: true
        },
        {
          title: 'global.actions',
          thComp: TranslatedHeader,
          tdComp: MarketlistActions,
          visible: true,
          thClass: 'text-right',
          tdClass: 'text-right td-actions'
        }
      ],
      query: { sort: 'created_at', order: 'desc', limit: 100, s: '' },
      report_query : {bu_id : null, site_id: null, startDate : null, endDate : null},
      xprops: {
        module: 'MarketlistsIndex',
        route: 'marketlists',
        permission_prefix: 'marketlist_'
      },
      dataFields : {
        'Kode' : 'code',
        'Tgl dibuat' : 'created_at',
        'User' : 'user.name',
        'BU' : 'bu.name',
        'Site' : 'site.name',
        'Status' : 'status_label'
      }
    }
  },
  beforeDestroy() {
    this.resetState()
  },
  computed: {
    ...mapGetters('MarketlistsIndex', ['data', 'total', 'loading','jsonData', 'lists']),
  },
  watch: {
    query: {
      handler(query) {
        this.setQuery(query)
        this.fetchIndexData()
        this.filterData()
      },
      deep: true
    }
  },
  methods: {
    ...mapActions('MarketlistsIndex', [
      'fetchIndexData',
      'setQuery',
      'resetState'
    ]),
    handleCheckboxChange(row) {
      row.selected = !row.selected;
      console.log(row.selected)
    },
    logCheckedValues() {
      const obj = this.data.filter(row => row.selected);
      this.rawData = [];
      const self = this;

      obj.forEach(function(value) {
        value.items.forEach(function(val) {
          self.rawData.push({
            'Kode ML' : value.code,
            'BU': value.bu.name,
            'Site' : value.site.name ?? 'ML',
            'Kategori': val.item.category.name,
            'name': val.item.name + ' - ' + val.satuan,
            'tanggal_dibutuhkan': val.required_date,
            'qty' : val.qty,
            'notes' : val.notes ?? ''
          })
        })
      })
      console.log(this.rawData)
      this.$refs['modal-2'].show()
      
    },
    filterData() {
      console.log(this.jsonData)
      this.customFilename = this.startDate
      const start = new Date(this.startDate)
      const [year, month, day] = this.startDate.split("-");
      const outputDate = `${day}-${month}-${year}`;
      // Filter the data based on the selected start date and end date
      this.filteredData = this.jsonData.filter((row) => {
        const dateComponents = row.tanggal_dibutuhkan.split("-");
        const dateObject = new Date(
          parseInt(dateComponents[2]),
          parseInt(dateComponents[1]) - 1,
          parseInt(dateComponents[0])
        );
        const offsetInMinutes = dateObject.getTimezoneOffset(); // Get the time zone offset in minutes
        const itemDate = new Date(dateObject.setMinutes(dateObject.getMinutes() - offsetInMinutes )); // Add 420 minutes (7 hours) for GMT+7
        // console.log(itemDate)
        // console.log(start)
        // console.log(row.tanggal_dibutuhkan, outputDate)
        return (
          row.tanggal_dibutuhkan === outputDate
        );
      });
      const transformedData = {};
      const uniqueSites = [...new Set(this.filteredData.map(obj => obj.Site))];
      this.filteredData.forEach((item) => {
          // Extract the relevant properties from the item
          const { name, Site, qty } = item;

          // Check if the name exists in the transformedData object
          if (!transformedData[name]) {
            // If not, initialize the name as a property and set the department quantities to 0
            transformedData[name] = { name, total: 0 };
          }

          uniqueSites.forEach((value) => {
            // Set the quantity for the corresponding department in the transformedData object
            if(value !== Site) {
              transformedData[name][value] = transformedData[name][value] || 0;
            }
            if(value === Site) {
               transformedData[name][value] = transformedData[name][value] + Number(qty) || Number(qty);
            }

            // Calculate the total for each name
          })
          transformedData[name].total += Number(qty);
           //  console.log(transformedData)

      });

        // Convert the object values back to an array
        this.filteredData = Object.values(transformedData).map((data) => {
          // Extract the "total" property from the object
          const { total, ...rest } = data;
          // Create a new object with the desired column order (move "total" to the last column)
          return { ...rest, total };
        });

        console.log(this.filteredData);
    },
    getPdf() {
      if(!this.report_query.bu_id || !this.report_query.site_id || !this.report_query.startDate || !this.report_query.endDate) {
        alert('Harap isikan bu, site, dan rentang waktu')
        return
      }
    },
    updateBu(value) {
      this.report_query.bu_id = value
    },
    updateSite(value) {
      this.report_query.site_id = value
    },
    updateStartDate(value) {
      this.report_query.startDate = value
      console.log(this.report_query.startDate)
    },
    updateEndDate(value) {
      this.report_query.endDate = value 
      console.log(this.report_query.endDate)
    },
    formatDate(value) {
      const dateString = value
      const year = new Date().getFullYear();
      const date = new Date(`${dateString} ${year}`);
      const formattedDate = `${date.getDate().toString().padStart(2, '0')}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getFullYear()}`;

      return formattedDate
    }
  }
}
</script>