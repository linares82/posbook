/*require('./bootstrap');*/

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/inertia-vue3'
import { InertiaProgress } from '@inertiajs/progress'

import Antd from 'ant-design-vue';
import 'ant-design-vue/dist/antd.css';


const appFil=createInertiaApp({
  resolve: name => require(`./Pages/${name}`),
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      //.mixin({methods:{route}})
      .use(Antd)
      .mount(el)
  },
})


InertiaProgress.init({color: '#00FAF6',showSpinner: true,})
