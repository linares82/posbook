<template>
<a-back-top />

<a-layout>
    <a-layout-content style="padding: 0 0px">
        <a-layout-header style="background: #6a05ca; color: floralwhite; fontsize: 2em">
            <a-row>
                <a-col :span="8">
                    <a-button type="secondary" style="margin-bottom: 16px" @click="() => (collapsed = !collapsed)">
                        <MenuUnfoldOutlined v-if="collapsed" />
                        <MenuFoldOutlined v-else />
                    </a-button>
                </a-col>
                <a-col :span="8">
                    <a-divider type="vertical" style="background-color: #fff" />
                    <Link href="/" as="button" class="ant-btn ant-btn-primary ant-btn-round">
                    <strong>PosBook</strong>
                  </Link>
                </a-col>
                <a-col :span="8">
                    <a-divider type="vertical" style="background-color: #fff" />
                    Bienvenido {{ $page.props.auth.user.username }}
                    <Link :href="linkPerfil" as="button" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm">Editar Perfil</Link>
                    <Link href="/logout" as="button" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm">Salir</Link>
                </a-col>
            </a-row>
        </a-layout-header>
    </a-layout-content>

    <a-config-provider :locale="es_ES">
        <a-layout-content style="padding: 5 5px">
            <a-layout style="padding: 0px 0; background: #fff" has-sider>
                <a-layout-sider v-model:collapsed="collapsed" :trigger="null" collapsible>
                    >
                    <div class="logo" />
                    <a-menu v-model:selectedKeys="selectedKeys" theme="dark" mode="inline">
                        <template v-for="item in $page.props.menudos" :key="item.key">
                            <template v-if="!item.children">
                                <a-menu-item :key="item.key">
                                    <template #icon>
                                        <PieChartOutlined />
                                    </template>
                                    <Link :href="item.link" target="_blank">{{ item.title }}</Link>
                                </a-menu-item>
                            </template>
                            <template v-else>
                                <sub-menu :key="item.key" :menu-info="item" />
                            </template>
                        </template>
                    </a-menu>
                </a-layout-sider>
                <a-layout-content :style="{
            background: '#fff',
            padding: '10px',
            margin: 0,
            minHeight: '280px',
          }">
                    <slot></slot>
                </a-layout-content>
            </a-layout>
        </a-layout-content>
    </a-config-provider>
</a-layout>
</template>
<script>
    import es_ES from 'ant-design-vue/lib/locale-provider/es_Es';
    import locale from 'ant-design-vue/es/date-picker/locale/es_Es';
    import dayjs from 'dayjs';
    import 'dayjs/locale/es-mx';
    dayjs.locale('es-mx');

    import {
        UserOutlined,
        VideoCameraOutlined,
        UploadOutlined,
        MenuUnfoldOutlined,
        MenuFoldOutlined,
        PieChartOutlined,
        MailOutlined,
    } from "@ant-design/icons-vue";
    import {
        ref
    } from "vue";
    import {
        Link
    } from "@inertiajs/inertia-vue3";

    const SubMenu = {
            name: "SubMenu",
            props: {
                menuInfo: {
                    type: Object,
                    default: () => ({}),
                },
            },
            template: `
    <a-sub-menu :key="menuInfo.key">
      <template #icon><MailOutlined /></template>
      <template #title>{{ menuInfo.title }}</template>
      <template v-for="item in menuInfo.children" :key="item.key">
        <template v-if="!item.children">
          <a-menu-item :key="item.key">
            <template #icon>
              <PieChartOutlined />
            </template>
            <a v-if="item.target" :href="item.link" target='_blank'>{{ item.title }}</a>
            <Link v-else :href="item.link" >{{ item.title }}</>
          </a-menu-item>
        </template>
        <template v-else>
          <sub-menu :menu-info="item" :key="item.key" />
        </template>
      
</template>

    </a-sub-menu>
  `,
  components: {
    PieChartOutlined,
    MailOutlined,
    Link,
  },
};
//const list = $page.props.menu;

export default {
  components: {
    UserOutlined,
    VideoCameraOutlined,
    UploadOutlined,
    MenuUnfoldOutlined,
    MenuFoldOutlined,
    "sub-menu": SubMenu,
    PieChartOutlined,
    Link,
  },

props:["menu","auth"],
  setup(props) {
    const list = props.menu;
    const collapsed = ref(true);
    const linkPerfil="/users/editPerfil/"+props.auth.user.id;

    return {
      linkPerfil,
      selectedKeys: ref(["1"]),
      collapsed: ref(true),
      list,
      selectedKeys: ref(["1"]),
      openKeys: ref(["2"]),
      es_ES,
      dayjs,
      locale
    };
  },
};
</script>

<style>
#components-layout-demo-custom-trigger .trigger {
    font-size: 18px;
    line-height: 64px;
    padding: 0 24px;
    cursor: pointer;
    transition: color 0.3s;
}

#components-layout-demo-custom-trigger .trigger:hover {
    color: #1890ff;
}

#components-layout-demo-custom-trigger .logo {
    height: 32px;
    background: rgba(255, 255, 255, 0.3);
    margin: 16px;
}

.site-layout .site-layout-background {
    background: #fff;
}
</style>
