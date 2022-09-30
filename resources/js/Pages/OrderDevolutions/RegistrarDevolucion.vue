<template>
<a-row>
    <a-col :span="12">
        <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="R. Devoluciones" sub-title="Ver" />
    </a-col>
    <a-col :span="12">
        <Link href="/orderDevolutions" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm" as="button">Regresar</Link>
    </a-col>
</a-row>
<a-row>
    <a-col :span="24">
        <a-descriptions title="Información" layout="vertical">
            <a-descriptions-item label="Id">{{orderDevolution.id}}</a-descriptions-item>
            <a-descriptions-item label="Nombre">{{orderDevolution.name}}</a-descriptions-item>
            <a-descriptions-item label="Fecha">{{orderDevolution.fecha}}</a-descriptions-item>
            <a-descriptions-item label="Motivo">{{orderDevolution.motivo}}</a-descriptions-item>
        </a-descriptions>
    </a-col>
</a-row>
<a-row>
    
    <a-col :span="24" v-show="lineas.length>0">
        <div style="background: #3C95BE; padding: 3px">
            <a-card title="Lineas">
                <table style="table-layout: auto;" class="ant-table-striped">
                    <thead class="ant-table-thead">
                        <tr>
                            <th>No.</th>
                            <th>Plantel</th>
                            <th>Producto</th>
                            <th>Cantidad Devolucion</th>
                            <th></th>
                        </tr>

                    </thead>
                    <tbody class="ant-table-tbody">
                        <tr v-for="(linea, index) in lineas" :key="index">
                            <td>{{index+1}}</td>
                            <td class="ant-table-cell">{{linea.plantel}}</td>
                            <td class="ant-table-cell">{{linea.product}}</td>
                            <td class="ant-table-cell">{{linea.cantidad}}</td>
                            <td>
                                <div v-if="linea.bnd_salida_registrada">
                                    SI
                                </div >
                                <div v-else>
                                    <a-popconfirm title="Esta seguro de proceder?" @confirm="registrarSalida(linea.id)">
                                        <a>Registrar Salida</a>
                                    </a-popconfirm>  
                                </div>
                                
                            </td>
                        </tr>
                    </tbody>
                </table>
            </a-card>
        </div>
    </a-col>
</a-row>
</template>

<script>
import Layout from "../../shared/Layout";
import {
    Inertia
} from "@inertiajs/inertia";
import {
    Link
} from "@inertiajs/inertia-vue3";
import axios from 'axios';
import {
    defineComponent,
    reactive,
    ref,
    watch,
    watchEffect
} from "vue";
import {
    MinusCircleOutlined,
} from "@ant-design/icons-vue";

export default {
    layout: Layout,

    components: {
        Link,
        MinusCircleOutlined
    },

    props: ["orderDevolution", 'orderDevolutionLines'],

    setup(props) {
        
        let lineas = reactive([]);

        for (let linea in props.orderDevolutionLines) {
            lineas.push({
                id: props.orderDevolutionLines[linea].id,
                order_devolution_id: props.orderDevolutionLines[linea].order_devolution_id,
                plantel_id: props.orderDevolutionLines[linea].plantel_id,
                plantel: props.orderDevolutionLines[linea].plantel,
                product_id: props.orderDevolutionLines[linea].product_id,
                product: props.orderDevolutionLines[linea].product,
                cantidad: props.orderDevolutionLines[linea].cantidad,
                bnd_salida_registrada: props.orderDevolutionLines[linea].bnd_salida_registrada
            });
        }

        
        const registrarSalida = key => {
            //console.log(key)
            //dataSource.value = dataSource.value.filter(item => item.id !== key);
            Inertia.get("/orderDevolutionLines/registrarDevolucion/"+key);
            location.reload;
        };
        

        

        

        return {
            
            registrarSalida,
            lineas,
            
        };
    }

};
</script>
