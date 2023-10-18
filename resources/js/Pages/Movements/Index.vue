<template>
    <a-row>
        <a-col :span="12">
            <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="Movimientos" sub-title="Lista" />
        </a-col>
        <a-col :span="12">
            <div v-if="permissions.movementsCreate">
                <Link href="/movements/create" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm" as="button">Crear
                </Link>
            </div>


        </a-col>
    </a-row>

    <a-collapse>
        <a-collapse-panel key="1" header="Buscar">
            <a-row>
                <a-col :span="6">
                    <a-select
                    :options="planteles"
                    v-model:value="search.plantel_id"
                    style="width: 200px"
                    placeholder="Seleccionar Plantel..."
                    show-search
                    :filter-option="filterOption"
                    >

                    </a-select>
                </a-col>
                <a-col :span="6">
                    <a-select
                    :options="motivos"
                    v-model:value="search.reason_id"
                    style="width: 200px"
                    placeholder="Seleccionar Motivo..."
                    show-search
                    :filter-option="filterOption"
                    >

                    </a-select>
                </a-col>
                <a-col :span="6">
                    <a-select
                    :options="tipo_movimientos"
                    v-model:value="search.type_movement_id"
                    style="width: 200px"
                    placeholder="Seleccionar Tipo Movimiento..."
                    show-search
                    :filter-option="filterOption"
                    >

                    </a-select>
                </a-col>
                <a-col :span="6">
                    <a-select
                    :options="productos"
                    v-model:value="search.product_id"
                    style="width: 200px"
                    placeholder="Seleccionar Producto..."
                    show-search
                    :filter-option="filterOption"
                    >

                    </a-select>
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
                                    <th @click="orderColumn('id')" class="ant-table-cell" colstart="0" colend="0">Id</th>
                                    <th @click="orderColumn('pu.plantel_id')" class="ant-table-cell" colstart="1" colend="1">Plantel</th>
                                    <th @click="orderColumn('reason_id')" class="ant-table-cell" colstart="1" colend="1">Motivo</th>
                                    <th class="ant-table-cell" colstart="1" colend="1">Tipo Movimiento</th>

                                    <th @click="orderColumn('product_id')" class="ant-table-cell" colstart="1" colend="1">Producto</th>
                                    <th class="ant-table-cell" colstart="1" colend="1">Entrada</th>
                                    <th class="ant-table-cell" colstart="1" colend="1">Salida</th>
                                    <th class="ant-table-cell" colstart="1" colend="1">Diferencia</th>
                                    <th class="ant-table-cell" colstart="1" colend="1">Caja</th>
                                    <th class="ant-table-cell" colstart="1" colend="1">Devolucion</th>
                                    <th class="ant-table-cell" colstart="3" colend="4">Acciones</th>
                                </thead>
                                <tbody class="ant-table-tbody">
                                    <tr class="ant-table-row ant-table-row-level-0" v-for="movement in movements.data"
                                        :key="movement.id">
                                        <td class="ant-table-cell" colstart="0" colend="0">{{ movement.id }}</td>
                                        <td class="ant-table-cell" colstart="1" colend="1">{{ movement.plantel }}</td>
                                        <td class="ant-table-cell" colstart="1" colend="1">{{ movement.motivo }} - {{ movement.order_sales_id }} Linea - {{ movement.order_sales_line_id }}</td>
                                        <td class="ant-table-cell" colstart="1" colend="1">{{ movement.tipo_movimiento }}</td>

                                        <td class="ant-table-cell" colstart="1" colend="1">{{ movement.producto }}</td>
                                        <td class="ant-table-cell" colstart="1" colend="1">{{ movement.entrada }}</td>
                                        <td class="ant-table-cell" colstart="1" colend="1">{{ movement.salida }}</td>
                                        <td class="ant-table-cell" colstart="1" colend="1">{{ movement.entrada-movement.salida }}</td>
                                        <td class="ant-table-cell" colstart="1" colend="1">Caja: {{ movement.cash_box_id }} Cantidad: {{ movement.quantity }}</td>
                                        <td class="ant-table-cell" colstart="1" colend="1">Devolución: {{ movement.order_devolution_id }} Cantidad: {{ movement.cantidad_devolucion }}</td>
                                        <td class="ant-table-cell" colstart="3" colend="4">
                                           <!--
                                            <a-dropdown-button>
                                                Acciones
                                                <template #overlay>
                                                    <a-menu>
                                                        <a-menu-item key="1" v-if="permissions.movementsEdit">

                                                            <Link :href="`/movements/edit/${movement.id}`"
                                                                class="ant-btn ant-btn-default ant-btn-round ant-btn-sm"
                                                                as="button">Editar</Link>
                                                        </a-menu-item>
                                                        <a-menu-item key="2" v-if="permissions.movementsDestroy">
                                                            <delete-outlined />
                                                            <a-popconfirm title="Estas seguro de la operación?"
                                                                ok-text="Si" cancel-text="No"
                                                                @confirm="borrar(movement.id)">
                                                                <button
                                                                    class="ant-btn ant-btn-default ant-btn-round ant-btn-sm">Borrar</button>
                                                            </a-popconfirm>
                                                        </a-menu-item>
                                                        <a-menu-item key="3" v-if="permissions.movementsShow">
                                                            <eye-outlined />

                                                            <Link :href="`/movements/show/${movement.id}`"
                                                                class="ant-btn ant-btn-default ant-btn-round ant-btn-sm"
                                                                as="button">Ver</Link>

                                                        </a-menu-item>
                                                    </a-menu>
                                                </template>
                                            </a-dropdown-button>
                                            -->
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

    <paginator :datos="movements"> </paginator>
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
    DeleteOutlined,
    EyeOutlined
} from "@ant-design/icons-vue";
import { message } from 'ant-design-vue';
import {
    watch,
    ref,
    reactive
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
        DeleteOutlined,
        EyeOutlined
    },

    props: ["movements", "filters", "sysMessage", 'permissions','planteles','motivos','tipo_movimientos','productos'],

    setup(props) {
        mounted: {
            if (props.sysMessage !== null) {
                message.success(props.sysMessage, 10);
            }

        };

        const borrar = (id) => {
            //if (confirm("estas seguro?")) {
            Inertia.delete(`/movements/delete/${id}`)
            //}
        };

        let search = reactive({
            plantel_id : props.filters.plantel_id,
            reason_id : props.filters.reason_id,
            type_movement_id : props.filters.type_movement_id,
            product_id : props.filters.product_id,
            column: props.filters.column,
            direction: props.filters.direction
        });
        watch(
            search,
            //reason_id,
            //type_movement_id,
            //product_id,
            debounce(function (value) {
                Inertia.get(
                    "/movements", {
                    search: value,
                    //reason_id: value,
                    //type_movement_id:value,
                    //product_id:value
                }, {
                    preserveState: true,
                    replace: true,
                }
                );
            }, 500)
        );

        const orderColumn = (column)=>{
            search.column=column;
            search.direction=search.direction === "asc" ? "desc" : "asc";
        }

        const filterOption = (input, option) => {
            //console.log(option);
            return option.label.toLowerCase().indexOf(input.toLowerCase()) >= 0;
        };

        return {
            borrar,
            search,
            paginator: false,
            orderColumn,
            filterOption
        };
    },
};
</script>

<style scoped>
</style>
