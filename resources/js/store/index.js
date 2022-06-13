import Vue from 'vue';
import Vuex from 'vuex';

import magusAligments from './modules/magus/magusAligments';
import magusRaces from './modules/magus/magusRaces';
import magusClasses from './modules/magus/magusClasses';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        magusAligments,
        magusRaces,
        magusClasses,
    }
})