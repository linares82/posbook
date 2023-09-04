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
                                <table style="table-layout: auto;" class="ant-table-striped">
                                    <colgroup></colgroup>
                                    <thead class="ant-table-thead">
                                        <tr>
                                            <th class="ant-table-cell" colspan="6" colstart="0" colend="5">
                                                <a-row type="flex" justify="center" align="top">
                                                    <a-col :span="24" style="text-align:center;"> {{cashBox.plantel}} </a-col>
                                                    <a-col :span="24" style="text-align:center;"> {{cashBox.director}} </a-col>
                                                    <a-col :span="24" style="text-align:center;"> {{cashBox.address}} </a-col>
                                                    <a-col :span="24" style="text-align:center;"> Tel. {{cashBox.phone}} </a-col>
                                                    <a-col :span="24" style="text-align:center;"> {{cashBox.created_at}} </a-col>
                                                </a-row>
                                            </th>
                                        </tr>
                                        <tr>
                                            <td>Producto </td><td>Cantidad</td><td>Monto</td>
                                        </tr>
                                    </thead>
                                    <tbody class="ant-table-tbody">
                                        <tr class="ant-table-row ant-table-row-level-0" v-for="linea in cashBoxLns" :key="linea.id">
                                            <td class="ant-table-cell" colstart="0" colend="0">{{ linea.id }} {{linea.product }} </td>
                                            <td class="ant-table-cell" colstart="0" colend="0">{{linea.quantity}}</td>
                                            <td class="ant-table-cell" colstart="2" colend="2" align="right">
                                                {{ Intl.NumberFormat('es-MX', {
                                                            style: 'currency', currency: 'MXN'
                                                        }).format(linea.total)}}

                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ant-table-cell" colstart="0" colend="0" colspan="2">Adeudo Total</td>
                                            <td class="ant-table-cell" colstart="2" colend="2" align="right">
                                                {{ Intl.NumberFormat('es-MX', {
                                                            style: 'currency', currency: 'MXN'
                                                        }).format(cashBox.total)}}

                                            </td>
                                        </tr>
                                        <tr v-for="payment in payments" :key="payment.id">
                                            <td class="ant-table-cell" colstart="0" colend="0">Pago realizado, {{payment.payment_method}}</td>
                                            <td class="ant-table-cell" colstart="2" colend="2"> $ {{ payment.monto}} </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <a-row type="flex" justify="center" align="top">
                                                    <a-col :span="24" style="text-align:center;"> Estado Actual: {{cashBox.estatus}} </a-col>
                                                </a-row>
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
    </a-col>
</a-row>
</template>

<script>
import Layout from "../../shared/LayoutPrint";

export default {
    layout: Layout,

    components: {},

    props: ["cashBox", "cashBoxLns", "payments"],

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
