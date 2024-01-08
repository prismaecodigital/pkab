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
          </div>
          <div v-if="$can('export')" class="card-body row">
            <b-modal id="modal-1" title="Export Data">
              <div class="modal-body">
                <!-- Date inputs for start and end date selection -->
                <label for="start-date">Pilih tanggal :</label>
                <input type="date" id="start-date" v-model="startDate" @change="filterData" />
              </div>
              <div class="modal-body">
                <!-- Button to close the modal -->

                <!-- Export button inside the modal -->
                <export-excel :data="filteredData" :name="customFilename">
                  <button class="btn btn-success">Export</button>
                </export-excel>
              </div>
            </b-modal>
          </div>
          <div class="card-body row">
            <b-modal id="modal-2" ref="modal-2" title="Export Data 2">
              <div class="modal-body">
                <button class="btn btn-success"><export-excel :data="rawData">
                    Download
                </export-excel></button>
              </div>
            </b-modal>
          </div>
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
    ...mapGetters('MarketlistsIndex', ['data', 'total', 'loading','jsonData']),
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
      // console.log(this.jsonData)
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

        console.log(typeof this.filteredData);
    },
  }
}
</script>