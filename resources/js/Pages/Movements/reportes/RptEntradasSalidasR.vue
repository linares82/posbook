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
                                        <h3>Cajas con Apartados</h3>
                                    </a-row>
                                    <table style="table-layout: auto;" class="ant-table-striped">
                                        <colgroup></colgroup>
                                        <thead class="ant-table-thead">
                                            <th>id</th>
                                            <th>Fecha Caja</th>
                                            <th>Cliente</th>
                                            <th>Matricula</th>
                                            <th>Entregado</th>
                                            <th>Stock</th>
                                            <th>Estatus</th>
                                            <th>Producto</th>
                                            <th>Cantidad</th>
                                            <th>Total Caja</th>
                                            <!--<th>M. Pago</th>
                                            <th>Monto</th>
                                            <th>% Descuento</th>
                                            <th>Fecha Pago</th>
                                            -->
                                        </thead>
                                        <tbody class="ant-table-tbody">
                                            <tr class="ant-table-row ant-table-row-level-0" v-for="caja in cajasApartadas" :key="caja.id">
                                                <td class="ant-table-cell" colstart="0" colend="0">{{ caja.id }}</td>
                                                <td class="ant-table-cell" colstart="2" colend="2">  {{ caja.fecha_caja}} </td>
                                                <td class="ant-table-cell" colstart="2" colend="2">  {{ caja.customer}} </td>
                                                <td class="ant-table-cell" colstart="2" colend="2">  {{ caja.matricula}} </td>
                                                <td class="ant-table-cell" colstart="2" colend="2">  {{ caja.bnd_entregado ? 'SI' : 'NO'}} </td>
                                                <td class="ant-table-cell" colstart="2" colend="2">  {{ caja.movement_id ? 'SI' : 'NO'}} </td>
                                                <td class="ant-table-cell" colstart="2" colend="2">  {{ caja.stcb}} </td>
                                                <td class="ant-table-cell" colstart="2" colend="2">  {{ caja.producto}} </td>
                                                <td class="ant-table-cell" colstart="2" colend="2">  {{ caja.quantity}} </td>
                                                <td class="ant-table-cell" colstart="2" colend="2">  {{ caja.total_caja}} </td>
                                                <!--<td class="ant-table-cell" colstart="2" colend="2">  {{ caja.payment_method}} </td>
                                                <td class="ant-table-cell" colstart="2" colend="2">  {{ caja.monto}} </td>
                                                <td class="ant-table-cell" colstart="2" colend="2">  {{ caja.porcentaje_descuento}} </td>
                                                <td class="ant-table-cell" colstart="2" colend="2">  {{ caja.fecha_pago}} </td>
                                                -->
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
    
    export default {
        layout: Layout,
    
        components: {},
    
        props: ['cajasApartadas'],
    
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
    