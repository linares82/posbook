<template>
    <a-row>
        <a-col :span="12">
            <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="Orden de Compra" sub-title="Lista" />
        </a-col>
        <a-col :span="12">
            <div v-if="permissions.orderSalesCreate">
                <Link href="/orderSales/create" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm" as="button">Crear
                </Link>
            </div>


        </a-col>
    </a-row>

    <a-collapse>
        <a-collapse-panel key="1" header="Buscar">
            <a-row>
                <a-col :span="6">
                    <a-input v-model:value="fecha" size="small" placeholder="Buscar...">
                        <template #prefix>
                            <search-outlined />
                        </template>
                        <template #suffix>
                            <a-tooltip title="Buscar...">
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
                                    <th class="ant-table-cell" colstart="1" colend="1">Fecha</th>
                                    <th class="ant-table-cell" colstart="1" colend="1">Nombre</th>
                                    <th class="ant-table-cell" colstart="1" colend="1">Plantel</th>
                                    <th class="ant-table-cell" colstart="3" colend="4">Acciones</th>
                                </thead>
                                <tbody class="ant-table-tbody">
                                    <tr class="ant-table-row ant-table-row-level-0" v-for="orderSale in orderSales.data"
                                        :key="orderSale.id">
                                        <td class="ant-table-cell" colstart="0" colend="0">{{ orderSale.id }}</td>
                                        <td class="ant-table-cell" colstart="1" colend="1">{{ orderSale.fecha }}</td>
                                        <td class="ant-table-cell" colstart="1" colend="1">{{ orderSale.name }}</td>
                                        <td class="ant-table-cell" colstart="1" colend="1">{{ orderSale.plantel }}</td>
                                        <td class="ant-table-cell" colstart="3" colend="4">
                                            <a-dropdown-button>
                                                Acciones
                                                <template #overlay>
                                                    <a-menu>
                                                        <a-menu-item key="1" v-if="permissions.orderSalesEdit">
                                                            <form-outlined />
                                                            <Link :href="`/orderSales/edit/${orderSale.id}`"
                                                                class="ant-btn ant-btn-default ant-btn-round ant-btn-sm"
                                                                as="button">Editar</Link>
                                                        </a-menu-item>
                                                        <a-menu-item key="1" v-if="permissions.orderSalesEdit">
                                                            <printer-outlined />
                                                            <a target="_blank" :href="`/orderSales/print/${orderSale.id}`"
                                                                class="ant-btn ant-btn-default ant-btn-round ant-btn-sm"
                                                                as="button">Imprimir</a>
                                                        </a-menu-item>
                                                        <a-menu-item key="2" v-if="permissions.orderSalesDestroy">
                                                            <delete-outlined />
                                                            <a-popconfirm title="Estas seguro de la operación?"
                                                                ok-text="Si" cancel-text="No"
                                                                @confirm="borrar(orderSale.id)">
                                                                <button
                                                                    class="ant-btn ant-btn-default ant-btn-round ant-btn-sm">Borrar</button>
                                                            </a-popconfirm>
                                                        </a-menu-item>
                                                        <a-menu-item key="3" v-if="permissions.orderSalesShow">
                                                            <eye-outlined />

                                                            <Link :href="`/orderSales/show/${orderSale.id}`"
                                                                class="ant-btn ant-btn-default ant-btn-round ant-btn-sm"
                                                                as="button">Registrar Entrada</Link>

                                                        </a-menu-item>
                                                        <a-menu-item key="4" v-if="permissions.orderDevolutionsCreate">
                                                            <eye-outlined />

                                                            <Link :href="`/orderDevolutions/create/${orderSale.id}`"
                                                                class="ant-btn ant-btn-default ant-btn-round ant-btn-sm"
                                                                as="button">Crear Devolución</Link>

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

    <paginator :datos="orderSales"> </paginator>
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
    EyeOutlined,
    PrinterOutlined,
} from "@ant-design/icons-vue";
import { message } from 'ant-design-vue';
import {
    watch,
    ref
} from "vue";
import {
    Inertia
} from "@inertiajs/inertia";
import debounce from "lodash/debounce";

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
        EyeOutlined,
        PrinterOutlined
    },

    props: ["orderSales", "filters", "sysMessage", 'permissions'],

    setup(props) {
        //console.log(props.sysMessage);
        mounted: {
            if (props.sysMessage !== null) {
                message.success(props.sysMessage, 10);
            }

        };

        const borrar = (id) => {
            //if (confirm("estas seguro?")) {
            Inertia.delete(`/orderSales/delete/${id}`)
            //}
        };

        let fecha = ref(props.filters.fecha);
        watch(
            fecha,
            debounce(function (value) {
                Inertia.get(
                    "/orderSales", {
                    fecha: value
                }, {
                    preserveState: true,
                    replace: true,
                }
                );
            }, 500)
        );
        return {
            borrar,
            fecha,
            paginator: false,

        };
    },
};
</script>

<style scoped>
</style>
