<template>
    <a-row>

        <a-col :span="24" style="background-color: #fff;">

            <div class="ant-table-wrapper ant-table-striped">
                <div class="ant-spin-nested-loading">
                    <div class="ant-spin-container">
                        <div class="ant-table ant-table-small ant-table-bordered">
                            <!---->
                            <div class="ant-table-container">
                                <div class="ant-table-content">
                                    <a-row type="flex" justify="center" align="top">
                                        <h3>Corte Plantel</h3>

                                    </a-row>
                                    <a-row type="flex" justify="center" align="top">

                                        <h4>Plantel: {{ plantel.name }}</h4>

                                    </a-row>
                                    <a-row type="flex" justify="center" align="top">
                                        <h4> Del {{ fecha1 }} Al {{ fecha2 }}</h4>

                                    </a-row>
                                    <table style="table-layout: auto;" class="ant-table-striped">
                                        <colgroup></colgroup>
                                        <thead class="ant-table-thead">
                                            <th>Libro</th>
                                            <th>Pedido</th>
                                            <th>Vales</th>
                                            <th>Vendidos</th>
                                            <th>Existencia despues de ventas</th>
                                            <th>P.U.</th>
                                            <th>Efectivo</th>
                                        </thead>
                                        <tbody class="ant-table-tbody">
                                            <tr class="ant-table-row ant-table-row-level-0" v-for="ln in resumen" :key="ln.movement_id">
                                                <td class="ant-table-cell" colstart="0" colend="0">{{ ln.producto }}</td>
                                                <td class="ant-table-cell" colstart="2" colend="2">  {{ ln.cantidad}} </td>
                                                <td class="ant-table-cell" colstart="2" colend="2">  {{ ln.vales}} </td>
                                                <td class="ant-table-cell" colstart="2" colend="2">  {{ ln.vendidos}} </td>
                                                <td class="ant-table-cell" colstart="2" colend="2">  {{ ln.existencia}} </td>
                                                <td class="ant-table-cell" colstart="2" colend="2">  {{ ln.precio}} </td>
                                                <td class="ant-table-cell" colstart="2" colend="2">  {{ ln.efectivo_caja}} </td>


                                            </tr>
                                            <tr class="ant-table-row ant-table-row-level-0" >
                                                <td class="ant-table-cell" colstart="0" colend="0">Totales</td>
                                                <td class="ant-table-cell" colstart="2" colend="2">  {{ totales.cantidad}} </td>
                                                <td class="ant-table-cell" colstart="2" colend="2">  {{ totales.vales}} </td>
                                                <td class="ant-table-cell" colstart="2" colend="2">  {{ totales.vendidos}} </td>
                                                <td class="ant-table-cell" colstart="2" colend="2">  {{ totales.existencia}} </td>
                                                <td class="ant-table-cell" colstart="2" colend="2">  {{ totales.precio}} </td>
                                                <td class="ant-table-cell" colstart="2" colend="2">  {{ totales.efectivo_caja}} </td>

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
    import Layout from "../../../shared/LayoutPrint";
import es_ES from 'ant-design-vue/lib/locale-provider/es_Es';

    export default {
        layout: Layout,

        components: {},

        props: ['resumen','plantel','fecha1','fecha2','totales'],

        setup(props) {
            console.log(props)

            return {

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
