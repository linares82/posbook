<template>
<a-row>
    <a-col :span="12">
        <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="Orden de Compra" sub-title="Editar" />
    </a-col>
    <a-col :span="12">
        <Link href="/orderSales" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm" as="button">Regresar</Link>
    </a-col>
</a-row>
<a-form :model="formOrderSale" @submit.prevent="submitF" autocomplete="off" layout="vertical" ref="formRef">
    <a-row>

        <a-col :md="7">
            <a-form-item label="Fecha" name="fecha">
                <a-date-picker v-model:value="formOrderSale.fecha" :bordered="true" />
                <div v-if="errors.fecha">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.fecha"></div>
                </div>
            </a-form-item>
        </a-col>

        <a-col :md="7">
            <a-form-item label="Nombre" name="name">
                <a-input v-model:value="formOrderSale.name" :bordered="true" />
                <div v-if="errors.name">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.name"></div>
                </div>
            </a-form-item>
        </a-col>

        <a-col :md="7">
            <a-form-item compact label="Plantel" name="plantel_id" :rules="[{ required: true, message: 'Por favor captura la información solicitada!' }]">
                <a-select :options="planteles" show-search v-model:value="formOrderSale.plantel_id" style="width: 300px" placeholder="Seleccionar Opción" :filter-option="filterOption">

                </a-select>
                <div v-if="errors.plantel_id">
                    <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.plantel_id"></div>
                </div>
            </a-form-item>
        </a-col>

        <a-col :span="24">
            <a-space v-for="(linea, index) in formOrderSale.lineas" :key="linea.tiempo_id" style="display: flex; margin-bottom: 8px" align="baseline">
                <a-form-item :name="['linea', index, 'plantel_id']" label="Plantel">
                    <a-select v-model:value="linea.plantel_id" :options="planteles" style="width: 300px" show-search :filter-option="filterOption"></a-select>
                </a-form-item>

                <a-form-item :name="['linea', index, 'product_id']" label="Producto">
                    <a-select v-model:value="linea.product_id" :options="productos" style="width: 300px" show-search :filter-option="filterOption"></a-select>
                </a-form-item>

                <a-form-item label="Cantidad" :name="['linea', index, 'cantidad']">
                    <a-input v-model:value="linea.cantidad" style="width: 200px" />
                </a-form-item>

                <MinusCircleOutlined @click="removeLinea(linea)" />
            </a-space>
        </a-col>

        <a-col :span="1"></a-col>

    </a-row>

    <a-form-item>
        <a-button type="dashed" block @click="addLinea">
            <PlusOutlined />
            Agregar Linea
        </a-button>
    </a-form-item>

    <a-form-item>
        <a-button type="primary" html-type="submit" :disabled="processing">Actualizar</a-button>
    </a-form-item>
</a-form>

<strong>Lineas </strong>

<a-table :columns="columns" :data-source="dataSource" bordered>
    <template #bodyCell="{ column, text, record }">
        <template v-if="['plantel_id'].includes(column.dataIndex)">
            <div>
                <a-select v-if="editableData[record.id]" :options="planteles" v-model:value="editableData[record.id][column.dataIndex]"  style="width: 200px" show-search :filter-option="filterOption"></a-select>
                <template v-else>
                    {{ text }}
                </template>
            </div>
        </template>
        <template v-if="['plantel','product'].includes(column.dataIndex)">
            <div>

                    {{ text }}

            </div>
        </template>
        <template v-if="['product_id'].includes(column.dataIndex)">
            <div>
                <a-select v-if="editableData[record.id]" :options="productos" v-model:value="editableData[record.id][column.dataIndex]"  style="width: 200px" show-search :filter-option="filterOption"></a-select>
                <template v-else>
                    {{ text }}
                </template>
            </div>
        </template>
        <template v-if="['cantidad','contacto'].includes(column.dataIndex)">
            <div>
                <a-input v-if="editableData[record.id]" v-model:value="editableData[record.id][column.dataIndex]" style="width:200px; margin: -5px 0" />
                <template v-else>
                    {{ text }}
                </template>
            </div>
        </template>
        <template v-else-if="column.dataIndex === 'operation'">
            <div class="editable-row-operations">
                <span v-if="editableData[record.id]">
                    <a-typography-link @click="save(record.id)">Guardar</a-typography-link>
                    <a-divider type="vertical" style="background-color: #9AA2C2" />
                    <a-popconfirm title="Estas seguro de cancelar?" @confirm="cancel(record.id)">
                        <a>Cancelar</a>
                    </a-popconfirm>
                </span>
                <span v-else>
                    <a @click="edit(record.id)">Editar</a>
                </span>
                <a-popconfirm v-if="dataSource.length" title="Estas seguro de borrar?" @confirm="onDelete(record.id)">
                  <a>Borrar</a>
                </a-popconfirm>
            </div>
        </template>
    </template>
</a-table>

</template>

<script>
import Layout from "../../shared/Layout";
import {
    Link
} from "@inertiajs/inertia-vue3";
import {
    Inertia
} from "@inertiajs/inertia";
import {
    cloneDeep
} from 'lodash-es';
import debounce from "lodash/debounce";
import {
    defineComponent,
    reactive,
    ref,
    watch
} from "vue";
import {
    MinusCircleOutlined,
    PlusOutlined,
    UserOutlined,
    LockOutlined
} from "@ant-design/icons-vue";
import dayjs from 'dayjs';

const columns = [{
    title: 'Id',
    dataIndex: 'id',
    width: '10%',
}, {
    title: 'Plantel',
    dataIndex: 'plantel_id',
    width: '10%',
}, {
    title: 'Plantel',
    dataIndex: 'plantel',
    width: '10%',
}, {
    title: 'Producto',
    dataIndex: 'product_id',
    width: '10%',
}, {
    title: 'Producto',
    dataIndex: 'product',
    width: '10%',
},{
    title: 'Cantidad',
    dataIndex: 'cantidad',
    width: '10%',
}, {
    title: 'Contacto',
    dataIndex: 'contacto',
    width: '20%',
},{
    title: 'Operacion',
    dataIndex: 'operation',
}];


export default {
    layout: Layout,
    components: {
        Link,
        UserOutlined,
        LockOutlined,
        MinusCircleOutlined,
        PlusOutlined,
    },

    props: ["errors", "orderSale", 'productos', 'planteles', 'lineas'],

    setup(props) {

        //Codigo para enviar nuevas linea
        const formRef = ref();
        let formOrderSale = reactive({
            id: props.orderSale.id,
            fecha: dayjs(props.orderSale.fecha, 'YYYY/MM/DD'),
            name: props.orderSale.name,
            plantel_id: props.orderSale.plantel_id,
            lineas: []
        });

        let processing = ref(false);

        const removeLinea = item => {
            let index = formOrderSale.lineas.indexOf(item);

            if (index !== -1) {
                formOrderSale.lineas.splice(index, 1);
            }
        };

        const addLinea = () => {
            formOrderSale.lineas.push({
                plantel_id: undefined,
                product_id: undefined,
                cantidad: undefined,
                contacto: undefined,
                tiempo_id: Date.now(),
            });
        };

        let submitF = () => {
            Inertia.post("", formOrderSale, {
                onStart: () => {
                    processing.value = true;
                },
                onFinish: () => {
                    processing.value = false;
                },
            });
        };
        //Fin formulario edicion cabecera

        const dataSource = ref(props.lineas);
        const editableData = reactive({});

        const edit = key => {
            editableData[key] = cloneDeep(dataSource.value.filter(item => key === item.id)[0]);
        };

        const save = key => {
            //console.log(editableData[key].current_stock);
            Object.assign(dataSource.value.filter(item => key === item.id)[0], editableData[key]);

            Inertia.post(
                "/orderSalesLines/edit/" + editableData[key].id, {
                    plantel_id:editableData[key].plantel_id,
                    product_id:editableData[key].product_id,
                    cantidad: editableData[key].cantidad,
                    contacto: editableData[key].contacto,
                }, {
                    preserveState: true,
                    replace: true,
                }
            );

            delete editableData[key];
        };

        const cancel = key => {
            delete editableData[key];
        };

        const onDelete = key => {
            //console.log(key)
            dataSource.value = dataSource.value.filter(item => item.id !== key);
            Inertia.delete(`/orderSalesLines/delete/${key}`, {
                    replace: true,
                })
            window.location.reload();
        };

        const filterOption = (input, option) => {
            //console.log(option);
            return option.label.toLowerCase().indexOf(input.toLowerCase()) >= 0;
        };

        return {
            onDelete,
            formOrderSale,
            submitF,
            processing,
            formRef,
            removeLinea,
            addLinea,
            dayjs,
            dataSource,
            columns,
            editingKey: '',
            editableData,
            edit,
            save,
            cancel,
            filterOption
        };
    },
};
</script>

<style scoped>
.editable-row-operations a {
    margin-right: 8px;
}
</style>
