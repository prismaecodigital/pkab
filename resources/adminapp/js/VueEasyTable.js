import "vue-easytable/libs/theme-default/index.css"; // import style
import Vue from "vue";
import { VeTable } from "vue-easytable";
import prop from "./mixins/prop";

Vue.use(VeTable);

const VeMixinTable = {
    mixins: [prop],
};

export default VeMixinTable;