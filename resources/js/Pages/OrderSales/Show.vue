<template>
<a-row>
    <a-col :span="12">
        <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="Orden de Compra" sub-title="Ver" />
    </a-col>
    <a-col :span="12">
        <Link href="/orderSales" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm" as="button">Regresar</Link>
    </a-col>
</a-row>
<a-row>
    <a-col :span="24">
        <a-descriptions title="Información" layout="vertical">
            <a-descriptions-item label="Id">{{orderSale.id}}</a-descriptions-item>
            <a-descriptions-item label="Nombre">{{orderSale.name}}</a-descriptions-item>
            <a-descriptions-item label="Fecha">{{orderSale.fecha}}</a-descriptions-item>
        </a-descriptions>
    </a-col>
</a-row>
<a-row>
    <a-col :span="24">
        <a-table bordered :data-source="dataSource" :columns="columns">
            <template #bodyCell="{ column, text, record }">
                <template v-if="column.dataIndex === 'name'">
                    <div class="editable-cell">
                        {{ text || ' ' }}
                    </div>
                </template>
                <template v-if="column.dataIndex === 'bnd_entrada_registrada'">
                    <div class="editable-cell" v-if="text==1">
                        SI
                    </div>
                </template>
                <template v-else-if="column.dataIndex === 'operation'">

                    <a-dropdown-button>
                        Acciones
                        <template #overlay>
                            <a-menu>
                                <a-menu-item key="1">
                                    <a-button type="primary" v-show="record.total_entradas<record.cantidad" @click="showModalEntrada(record)">Reg. Entrada</a-button>
                                </a-menu-item>
                                <a-menu-item key="2">
                                    <a-button type="primary" @click="showModalObs(record)">Crear Obs.</a-button>
                                </a-menu-item>
                                <a-menu-item key="3">
                                    <a-button type="primary" @click="showDrawerEntradas(record)">Ver Entradas</a-button>
                                </a-menu-item>
                                <a-menu-item>
                                    <a-button type="primary" @click="showDrawer(record)">Ver Obs.</a-button>
                                </a-menu-item>

                            </a-menu>
                        </template>
                    </a-dropdown-button>

                </template>
            </template>

        </a-table>
    </a-col>

</a-row>

<a-drawer :width="500" title="Consulta" :placement="placement" :visible="visibleDrawer" @close="onClose">
    <template #extra>
        <a-button style="margin-right: 8px" @click="onClose">Cancel</a-button>
    </template>
    <table v-show="formEntrada.observaciones.length>0" style="table-layout: auto;" class="ant-table-striped">
        <thead class="ant-table-thead">
            <th class="ant-table-cell" colstart="0" colend="0">Observacion</th>
            <th class="ant-table-cell" colstart="0" colend="0">Creado Por</th>
            <th class="ant-table-cell" colstart="0" colend="0">Creado El</th>
            <th></th>
        </thead>
        <tbody class="ant-table-tbody">
            <tr class="ant-table-row ant-table-row-level-0" v-for="(obs, index) in formEntrada.observaciones" :key="obs.id">
                <td class="ant-table-cell" colstart="0" colend="0">{{obs.observation}}</td>
                <td class="ant-table-cell" colstart="0" colend="0">{{obs.user_alta}}</td>
                <td class="ant-table-cell" colstart="0" colend="0">{{obs.created_at}}</td>
                <td>
                    <a-popconfirm title="Estas seguro de la operación?" ok-text="Si" cancel-text="No" @confirm="eliminarObs(obs)">
                        <button class="ant-btn ant-btn-default ant-btn-round ant-btn-sm">Borrar</button>
                    </a-popconfirm>
                </td>
            </tr>
        </tbody>

    </table>
    <table v-show="formEntrada.entradas.length>0" style="table-layout: auto;" class="ant-table-striped">
        <thead class="ant-table-thead">
            <th class="ant-table-cell" colstart="0" colend="0">Producto</th>
            <th class="ant-table-cell" colstart="1" colend="1">Cantidad Entrada</th>
            <th class="ant-table-cell" colstart="2" colend="2">Cantidad Salida</th>
            <th class="ant-table-cell" colstart="3" colend="3">Creado Por</th>
            <th class="ant-table-cell" colstart="4" colend="4">Creado El</th>
            <th></th>
        </thead>
        <tbody class="ant-table-tbody">
            <tr class="ant-table-row ant-table-row-level-0" v-for="(entrada, index) in formEntrada.entradas" :key="entrada.id">
                <td class="ant-table-cell" colstart="0" colend="0">{{entrada.producto}}</td>
                <td class="ant-table-cell" colstart="1" colend="1">{{entrada.cantidad_entrada}}</td>
                <td class="ant-table-cell" colstart="2" colend="2">{{entrada.cantidad_salida}}</td>
                <td class="ant-table-cell" colstart="3" colend="3">{{entrada.user_alta}}</td>
                <td class="ant-table-cell" colstart="4" colend="4">{{entrada.created_at}}</td>
                <td>
                    <a-popconfirm title="Estas seguro de la operación?" ok-text="Si" cancel-text="No" @confirm="eliminarEntrada(entrada)">
                        <button class="ant-btn ant-btn-default ant-btn-round ant-btn-sm">Borrar</button>
                    </a-popconfirm>
                </td>
            </tr>
        </tbody>

    </table>
</a-drawer>

<div>
    <a-modal v-model:visible="visibleEntrada" title="Agregar Entrada">
        <template #footer>
            <a-button key="back" @click="handleCancelEntrada">Cancelar</a-button>
            <a-button key="submit" type="primary" :loading="loadingObs" @click="addEntrada">Agregar</a-button>
        </template>
        <a-col :md="24">

            <a-input type="hidden" name="plantel_id" v-model:value="formEntrada.plantel_id" />

            <a-input type="hidden" name="order_sales_line_id" v-model:value="formEntrada.order_sales_line_id" />

            <a-input type="hidden" name="product_id" v-model:value="formEntrada.product_id" />

            <a-form-item label="Cantidad Entrada" name="cantidad_entrada">
                <a-input v-model:value="formEntrada.cantidad_entrada"> </a-input>
                <div v-if="errors.cantidad_entrada">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.cantidad_entrada"></div>
                </div>
            </a-form-item>
        </a-col>
    </a-modal>
</div>

<div>
    <a-modal v-model:visible="visibleObs" title="Agregar Observacion">
        <template #footer>
            <a-button key="back" @click="handleCancelObs">Cancelar</a-button>
            <a-button key="submit" type="primary" :loading="loadingObs" @click="addObs">Agregar</a-button>
        </template>
        <a-col :md="24">

            <a-input type="hidden" name="order_sales_line_id" v-model:value="formObs.order_sales_line_id" />
            <a-form-item label="Observacion" name="observation">
                <a-input v-model:value="formObs.observation"> </a-input>
                <div v-if="errors.observation">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.observation"></div>
                </div>
            </a-form-item>
        </a-col>
    </a-modal>
</div>
</template>

<script>
import Layout from "../../shared/Layout";
import {
    Link
} from "@inertiajs/inertia-vue3";
import {
    computed,
    defineComponent,
    reactive,
    ref
} from 'vue';
import {
    CheckOutlined,
    EditOutlined
} from '@ant-design/icons-vue';
import {
    cloneDeep
} from 'lodash-es';
import {
    Inertia
} from "@inertiajs/inertia";
import axios from 'axios';
import dayjs from 'dayjs';

export default {
    layout: Layout,

    components: {
        Link,
        CheckOutlined,
        EditOutlined,
    },

    props: ["orderSale", "lineas", "errors", 'route_verObservaciones','route_verEntradas'],

    setup(props) {
        const columns = [{
            title: 'Id',
            dataIndex: 'id',
            width: '10%',
        }, {
            title: 'Plantel',
            dataIndex: 'plantel',
            width: '10%',
        }, {
            title: 'Producto',
            dataIndex: 'product',
            width: '10%',
        }, {
            title: 'Cantidad',
            dataIndex: 'cantidad',
            width: '10%',
        }, {
            title: 'Contacto',
            dataIndex: 'contacto',
            width: '20%',
        }, {
            title: 'Total Entradas',
            dataIndex: 'total_entradas',
            width: '20%',
        }, {
            title: 'Operacion',
            dataIndex: 'operation',
        }];

        const dataSource = ref(props.lineas);
        const visibleEntrada = ref(false);
        const loadingEntrada = ref(false);
        let processing = ref(false);

        const count = computed(() => dataSource.value.length + 1);
        const editableData = reactive({});

        const formEntrada = reactive({
            id: '',
            plantel_id: '',
            product_id: '',
            cantidad_entrada: 0,
            order_sales_line_id: '',
            total_order_line: 0,
            total_acumulado: 0,
            observaciones: [],
            entradas: []
        });

        let observaciones = reactive([]);
        const placement = ref('bottom');
        const visibleDrawer = ref(false);

        const showModalEntrada = (record) => {
            visibleEntrada.value = true;
            //console.log(record);
            formEntrada.plantel_id = record.plantel_id;
            formEntrada.product_id = record.product_id;
            formEntrada.cantidad_entrada = record.cantidad - record.total_entradas;
            formEntrada.total_order_line = record.cantidad;
            formEntrada.total_acumulado = record.total_entradas;
            formEntrada.order_sales_line_id = record.id;
        };

        const addEntrada = record => {
            console.log(formEntrada)
            //dataSource.value = dataSource.value.filter(item => item.id !== key);
            //Inertia.get("/orderSalesLines/receiveOCPlantel/"+key);
            if (formEntrada.total_order_line <= (formEntrada.total_acumulado + formEntrada.cantidad_entrada)) {
                Inertia.post("/movements/store", formEntrada, {
                    onStart: () => {
                        processing.value = true;
                    },
                    onFinish: () => {
                        processing.value = false;
                    },
                });
                visibleEntrada.value = false;
            } else {
                
                alert("La suma de entradas ya registradas y la cantidad nueva de entradas es superior a la orden de compra");
            }

        };

        const handleCancelEntrada = () => {
            visibleEntrada.value = false;
        };

        const formObs = reactive({
            observation: '',
            order_sales_line_id: '',
        });
        const visibleObs = ref(false);
        const loadingObs = ref(false);
        const showModalObs = (record) => {
            visibleObs.value = true;
            //console.log(record);
            formObs.order_sales_line_id = record.id;
        };
        const addObs = record => {
            //console.log(record)
            //dataSource.value = dataSource.value.filter(item => item.id !== key);
            //Inertia.get("/orderSalesLines/receiveOCPlantel/"+key);

            Inertia.post("/obsEntries/store", formObs, {
                onStart: () => {
                    processing.value = true;
                },
                onFinish: () => {
                    processing.value = false;
                },
            });
            visibleObs.value = false;
        };
        const handleCancelObs = () => {
            visibleObs.value = false;
        };

        

        const showDrawer = (record) => {
            visibleDrawer.value = true;
            //console.log(observaciones);
            formEntrada.observaciones.splice(0, formEntrada.observaciones.length);
            formEntrada.entradas.splice(0, formEntrada.entradas.length);
            axios.get(props.route_verObservaciones +
                    "?order_sales_line_id=" + record.id)
                .then(response => {
                    //formCashBox.precio = response.data.precio;
                    //consultaProductos=response.data;
                    //console.log(response.data);
                    observaciones = response.data;

                    for (let i = 0; i < response.data.length; i++) {
                        formEntrada.observaciones.push({
                            id: response.data[i].id,
                            observation: response.data[i].observation,
                            created_at: dayjs(response.data[i].created_at).format('YYYY/MM/DD'),
                            user_alta: response.data[i].user_alta
                        });
                    }

                    //console.log(formEntrada.observaciones);
                });
            //console.log(observaciones);
        };

        const showDrawerEntradas = (record) => {
            visibleDrawer.value = true;
            //console.log(observaciones);
            formEntrada.observaciones.splice(0, formEntrada.observaciones.length);
            formEntrada.entradas.splice(0, formEntrada.entradas.length);
            axios.get(props.route_verEntradas +
                    "?order_sales_line_id=" + record.id)
                .then(response => {
                    //formCashBox.precio = response.data.precio;
                    //consultaProductos=response.data;
                    //console.log(response.data);

                    for (let i = 0; i < response.data.length; i++) {
                        formEntrada.entradas.push({
                            id: response.data[i].id,
                            producto:response.data[i].producto,
                            cantidad_entrada: response.data[i].cantidad_entrada,
                            cantidad_salida: response.data[i].cantidad_salida,
                            created_at: dayjs(response.data[i].created_at).format('YYYY/MM/DD'),
                            user_alta: response.data[i].user_alta,
                            movement_id:response.data[i].movement_id
                        });
                    }

                    //console.log(formEntrada.observaciones);
                });
            //console.log(observaciones);
        };

        const onClose = () => {
            visibleDrawer.value = false;
        };

        const eliminarObs = (record) => {
            Inertia.delete(`/obsEntries/delete/${record.id}`)
            visibleDrawer.value = false;
        };

        const eliminarEntrada = (record) => {
            //console.log(record);
            Inertia.delete(`/movements/delete/${record.id}`)
            visibleDrawer.value = false;
        };

        return {
            columns,
            showModalEntrada,
            addEntrada,
            dataSource,
            editableData,
            count,
            formEntrada,
            visibleEntrada,
            handleCancelEntrada,
            loadingEntrada,
            processing,
            formObs,
            visibleObs,
            handleCancelObs,
            loadingObs,
            showModalObs,
            addObs,
            observaciones,
            visibleDrawer,
            placement,
            showDrawer,
            onClose,
            eliminarObs,
            showDrawerEntradas,
            eliminarEntrada
        };
    },
};
</script>
