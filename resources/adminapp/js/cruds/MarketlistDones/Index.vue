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
                </datatable>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import MarketlistActions from '@components/Datatables/MarketlistActions'
import TranslatedHeader from '@components/Datatables/TranslatedHeader'
import HeaderSettings from '@components/Datatables/HeaderSettings'
import GlobalSearch from '@components/Datatables/GlobalSearch'
import DatatableSingle from '@components/Datatables/DatatableSingle'
import DatatableEnum from '@components/Datatables/DatatableEnum'

export default {
  components: {
    GlobalSearch,
    HeaderSettings
  },
  data() {
    return {
      columns: [
        {
          title: 'cruds.marketlist.fields.code',
          field: 'code',
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
      query: { sort: 'code', order: 'asc', limit: 100, s: '' },
      xprops: {
        module: 'MarketlistDonesIndex',
        route: 'marketlist-dones',
        permission_prefix: 'marketlist_'
      },
      dataFields : {
        'Kode' : 'code',
        'Tgl dibuat' : 'created_at',
        'Tgl dibutuhkan' : 'req_date',
        'User' : 'user.name',
        'BU' : 'dept.bu.name',
        'Dept/Site' : 'dept.name',
        'Status' : 'status_label'
      }
    }
  },
  beforeDestroy() {
    this.resetState()
  },
  computed: {
    ...mapGetters('MarketlistDonesIndex', ['data', 'total', 'loading']),
  },
  watch: {
    query: {
      handler(query) {
        this.setQuery(query)
        this.fetchIndexData()
      },
      deep: true
    }
  },
  methods: {
    ...mapActions('MarketlistDonesIndex', [
      'fetchIndexData',
      'setQuery',
      'resetState'
    ]),
  }
}
</script>