<template>
<!--<a-row class="oculto-impresion">
    <a-col :span="12">
        <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="Orden de Compra" sub-title="Imprimir" />
    </a-col>
    <a-col :span="12">
        <Link href="/orderSales" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm" as="button">Regresar</Link>
    </a-col>
</a-row>
-->
<a-row>

    <a-col :span="24" style="background-color: #fff;">
        <!--<div class="header">
            <a-page-header :ghost="false" title="CORPORATIVO" sub-title="Coordinacion Pedagógica">
                <template #extra>
                    ProfessionalWorking Center
                </template>
            </a-page-header>
            <a-row type="flex" justify="center" align="top">
                <a-col :span="6" style="text-align:center;"> Departamento de Inglés </a-col>
            </a-row>
            <a-row type="flex" justify="center" align="top">
                <a-col :span="6" style="text-align:center;"> Fecha de Solicitud {{orderSale.fecha}} </a-col>
            </a-row>
            <br>
        </div>-->
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
                                        <tr>
                                        <th class="ant-table-cell"  colspan="6" colstart="0" colend="5">
                                            <a-page-header :ghost="false" title="CORPORATIVO" sub-title="Coordinacion Pedagógica">
                                                <template #extra>
                                                    ProfessionalWorking Center
                                                </template>
                                            </a-page-header>
                                            <a-row type="flex" justify="center" align="top">
                                                <a-col :span="12" style="text-align:center;"> Departamento de Inglés </a-col>
                                            </a-row>
                                            <a-row type="flex" justify="center" align="top">
                                                <a-col :span="12" style="text-align:center;"> Fecha de Solicitud {{orderSale.fecha}} </a-col>
                                            </a-row>
                                        </th>
                                        </tr>
                                        <tr>
                                        <th class="ant-table-cell" colstart="0" colend="0">Id</th>
                                        <th class="ant-table-cell" colstart="1" colend="1">Plantel</th>
                                        <th class="ant-table-cell" colstart="2" colend="2">Producto</th>
                                        <th class="ant-table-cell" colstart="3" colend="3">Cantidad</th>
                                        <th class="ant-table-cell" colstart="4" colend="4">Dirección</th>
                                        <th class="ant-table-cell" colstart="5" colend="5">Contacto</th>
                                        </tr>
                                    </thead>
                                    <tbody class="ant-table-tbody">
                                        <tr class="ant-table-row ant-table-row-level-0" v-for="linea in lineas" :key="linea.id">
                                            <td class="ant-table-cell" colstart="0" colend="0">{{ linea.id }}</td>
                                            <td class="ant-table-cell" colstart="1" colend="1">{{ linea.plantel }}</td>
                                            <td class="ant-table-cell" colstart="2" colend="2">{{ linea.product }}</td>
                                            <td class="ant-table-cell" colstart="3" colend="3">{{ linea.cantidad }}</td>
                                            <td class="ant-table-cell" colstart="4" colend="4">{{ linea.address }}</td>
                                            <td class="ant-table-cell" colstart="5" colend="5">{{ linea.contacto }}</td>

                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </a-col>
</a-row>
</template>

<script>
import Layout from "../../shared/LayoutPrint";
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

export default {
    layout: Layout,

    components: {
        Link,
        CheckOutlined,
        EditOutlined,
    },

    props: ["orderSale", "lineas"],

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
            title: 'Entrada Registrada',
            dataIndex: 'bnd_entrada_registrada',
            width: '20%',
        }, {
            title: 'Operacion',
            dataIndex: 'operation',
        }];

        const dataSource = ref(props.lineas);

        const count = computed(() => dataSource.value.length + 1);
        const editableData = reactive({});

        const registrarEntrada = key => {
            //console.log(key)
            //dataSource.value = dataSource.value.filter(item => item.id !== key);
            Inertia.get("/orderSalesLines/receiveOCPlantel/" + key);
        };

        const imprSelec = nombre => {
            var ficha = document.getElementById(nombre);
            var ventimp = window.open(' ', 'popimpr');
            ventimp.document.write(ficha.innerHTML);
            ventimp.document.close();
            ventimp.print();
            ventimp.close();
        }

        return {
            columns,
            registrarEntrada,
            dataSource,
            editableData,
            count,
            imprSelec
        };
    },
};
</script>

<style>
@media print {

    .oculto-impresion,
    .oculto-impresion * {
        display: none !important;
    }

    header {
        position: fixed;
        top: 0;
    }
}

header {
    position: fixed;
    top: 0;
}
</style>
