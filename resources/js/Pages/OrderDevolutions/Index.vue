<template>
    <a-row>
        <a-col :span="12">
            <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="O. Devoluciones" sub-title="Lista" />
        </a-col>
        <a-col :span="12">
            <div v-if="permissions.orderDevolutionsCreate">
                <Link href="/orderDevolutions/create" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm" as="button">Crear
                </Link>
            </div>


        </a-col>
    </a-row>

    <a-collapse>
        <a-collapse-panel key="1" header="Buscar">
            <a-row>
                <a-col :span="6">
                    <a-input v-model:value="search.name" size="small" placeholder="Buscar..">
                        <template #prefix>
                            <search-outlined />
                        </template>
                        <template #suffix>
                            <a-tooltip title="Buscar ...">
                                <info-circle-outlined style="color: rgba(0, 0, 0, 0.45)" />
                            </a-tooltip>
                        </template>
                    </a-input>
                </a-col>

            </a-row>
        </a-collapse-panel>
    </a-collapse>

    <div class="ant-table-wrapper ant-table-striped">
        <div class="ant-spin-nested-loading">
            <div class="ant-spin-container">
                <div class="ant-table ant-table-small ant-table-bordered">
                    <!---->
                    <div class="ant-table-container">
                        <div class="ant-table-content">
                            <table style="table-layout: auto;" class="ant-table-striped">
                                <colgroup></colgroup>
                                <thead class="ant-table-thead">
                                    <th class="ant-table-cell" colstart="0" colend="0">Id</th>
                                    <th class="ant-table-cell" colstart="1" colend="1">O. Devolucion</th>
                                    <th class="ant-table-cell" colstart="1" colend="1">Fecha</th>
                                    <th class="ant-table-cell" colstart="1" colend="1">Desc.</th>
                                    <th class="ant-table-cell" colstart="3" colend="4">Acciones</th>
                                </thead>
                                <tbody class="ant-table-tbody">
                                    <tr class="ant-table-row ant-table-row-level-0" v-for="orderDevolution in orderDevolutions.data"
                                        :key="orderDevolution.id">
                                        <td class="ant-table-cell" colstart="0" colend="0">{{ orderDevolution.id }}</td>
                                        <td class="ant-table-cell" colstart="1" colend="1">{{ orderDevolution.name }}</td>
                                        <td class="ant-table-cell" colstart="1" colend="1">{{ orderDevolution.fecha }}</td>
                                        <td class="ant-table-cell" colstart="1" colend="1">{{ orderDevolution.desc }}</td>
                                        <td class="ant-table-cell" colstart="3" colend="4">
                                            <a-dropdown-button>
                                                Acciones
                                                <template #overlay>
                                                    <a-menu>
                                                        <a-menu-item key="1" v-if="permissions.orderDevolutionsEdit">
                                                            <form-outlined />
                                                            <Link :href="`/orderDevolutions/edit/${orderDevolution.id}`"
                                                                class="ant-btn ant-btn-default ant-btn-round ant-btn-sm"
                                                                as="button">Editar</Link>
                                                        </a-menu-item>
                                                        <a-menu-item key="2" v-if="permissions.orderDevolutionsDestroy">
                                                            <delete-outlined />
                                                            <a-popconfirm title="Estas seguro de la operación?"
                                                                ok-text="Si" cancel-text="No"
                                                                @confirm="borrar(orderDevolution.id)">
                                                                <button
                                                                    class="ant-btn ant-btn-default ant-btn-round ant-btn-sm">Borrar</button>
                                                            </a-popconfirm>
                                                        </a-menu-item>
                                                        <a-menu-item key="3" v-if="permissions.orderDevolutionsShow">
                                                            <eye-outlined />

                                                            <Link :href="`/orderDevolutions/show/${orderDevolution.id}`"
                                                                class="ant-btn ant-btn-default ant-btn-round ant-btn-sm"
                                                                as="button">Agregar Lineas</Link>

                                                        </a-menu-item>
                                                        <a-menu-item key="3" v-if="permissions.orderDevolutionsShow">
                                                            <eye-outlined />

                                                            <Link :href="`/orderDevolutions/registrarDevolucion/${orderDevolution.id}`"
                                                                class="ant-btn ant-btn-default ant-btn-round ant-btn-sm"
                                                                as="button">Registrar Salida Devolucion</Link>

                                                        </a-menu-item>
                                                    </a-menu>
                                                </template>
                                            </a-dropdown-button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <paginator :datos="orderDevolutions"> </paginator>
</template>

<script>
import Layout from "../../shared/Layout";
import Paginator from "../../shared/Paginator";
import {
    Link
} from "@inertiajs/inertia-vue3";
import {
    SmileOutlined,
    SearchOutlined,
    InfoCircleOutlined,
    FormOutlined,
    DeleteOutlined,
    EyeOutlined
} from "@ant-design/icons-vue";
import { message } from 'ant-design-vue';
import {
    Inertia
} from "@inertiajs/inertia";
import debounce from "lodash/debounce";
import {
    watch,
    ref,
    reactive,
    mounted
} from "vue";

export default {
    layout: Layout,

    components: {
        Link,
        Paginator,
        SearchOutlined,
        InfoCircleOutlined,
        SmileOutlined,
        FormOutlined,
        DeleteOutlined,
        EyeOutlined
    },

    props: ["orderDevolutions", "filters", "sysMessage", 'permissions','planteles'],

    setup(props) {
        mounted: {
            if (props.sysMessage !== null) {
                message.success(props.sysMessage, 10);
            }

        };

        const borrar = (id) => {
            //if (confirm("estas seguro?")) {
            Inertia.delete(`/orderDevolutions/delete/${id}`)
            //}
        };

        //let name = ref(props.filters.name);
        let search = reactive({
            name : props.filters.name,
            column: props.filters.column,
            direction: props.filters.direction
        });
        watch(
            search,
            debounce(function (value) {
                Inertia.get(
                    "/orderDevolutions", {
                    search: value
                }, {
                    preserveState: true,
                    replace: true,
                }
                );
            }, 500)
        );
        return {
            borrar,
            search,
            paginator: false,

        };
    },
};
</script>

<style scoped>
</style>
