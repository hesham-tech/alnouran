<template>
  <div class="d-inline-block">
    <v-dialog v-model="dialog" max-width="500">
      <template v-slot:activator="{ props }">
        <v-btn
          color="primary"
          prepend-icon="mdi-plus"
          @click="setField()"
          v-bind="props"
          class="rounded-lg font-weight-bold elevation-1"
        >
          طلب إجازة جديد
        </v-btn>
      </template>

      <v-card class="rounded-xl border overflow-hidden bg-surface">
        <div class="pa-6 border-b bg-surface-variant bg-opacity-5 d-flex align-center">
          <v-icon color="primary" class="mr-3">mdi-calendar-plus</v-icon>
          <span class="text-h6 font-weight-bold">طلب إجازة جديد</span>
          <v-spacer></v-spacer>
          <v-btn icon="mdi-close" variant="text" size="small" @click="dialog = false"></v-btn>
        </div>

        <v-card-text class="pa-6">
          <v-alert
            v-if="addRequest.Type != 'Rest allowance'"
            type="warning"
            variant="tonal"
            density="compact"
            class="mb-6 rounded-lg text-caption"
            border="start"
          >
            تنبيه: يرجى عدم تحديد أيام عطلات رسمية (الجمعة والسبت) ضمن الفترة.
          </v-alert>

          <v-form ref="form" @submit.prevent="saveRequest">
            <div class="d-flex align-center mb-4">
              <v-checkbox
                v-model="dailyFife"
                label="نصف يوم"
                color="error"
                hide-details
                density="compact"
              ></v-checkbox>
            </div>

            <v-select
              v-model="addRequest.Type"
              label="نوع الإجازة"
              :items="typeRequest"
              item-title="ar"
              item-value="en"
              variant="outlined"
              class="mb-4"
              prepend-inner-icon="mdi-format-list-bulleted-type"
              :rules="[v => !!v || 'هذا الحقل مطلوب']"
            ></v-select>

            <div v-if="addRequest.Type != 'Rest allowance'">
              <v-textarea
                v-model="addRequest.description"
                label="الوصف / السبب"
                variant="outlined"
                rows="2"
                auto-grow
                class="mb-4"
                prepend-inner-icon="mdi-text-subject"
                :rules="[v => !!v || 'هذا الحقل مطلوب']"
              ></v-textarea>
            </div>

            <div v-if="addRequest.Type == 'Rest allowance'">
              <v-select
                v-model="addRequest.rest_id"
                label="اختر من البدلات المتاحة"
                :items="activeRest"
                item-title="description"
                item-value="id"
                variant="outlined"
                class="mb-4"
                prepend-inner-icon="mdi-clock-check-outline"
                :rules="[v => !!v || 'هذا الحقل مطلوب']"
              ></v-select>
            </div>

            <v-row dense>
              <v-col cols="12" :sm="addRequest.Type == 'Rest allowance' ? 12 : 6">
                <v-text-field
                  v-model="addRequest.start_date"
                  :label="addRequest.Type == 'Rest allowance' ? 'تاريخ الإجازة' : 'بداية الإجازة'"
                  type="date"
                  variant="outlined"
                  class="mb-4"
                  prepend-inner-icon="mdi-calendar-start"
                ></v-text-field>
              </v-col>
              <v-col cols="12" sm="6" v-if="addRequest.Type != 'Rest allowance'">
                <v-text-field
                  v-model="addRequest.end_date"
                  label="نهاية الإجازة"
                  type="date"
                  variant="outlined"
                  class="mb-4"
                  prepend-inner-icon="mdi-calendar-end"
                ></v-text-field>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>

        <div class="pa-6 border-t d-flex justify-end gap-2 bg-surface-variant bg-opacity-5">
          <v-btn color="secondary" variant="text" @click="dialog = false" class="rounded-lg px-6">إلغاء</v-btn>
          <v-btn color="primary" @click="saveRequest" class="rounded-lg px-8 font-weight-bold">حفظ الطلب</v-btn>
        </div>
      </v-card>
    </v-dialog>
  </div>
    <v-dialog v-model="dialog2" width="auto">
        <v-card>
            <v-card-title v-if="Array.isArray(absenceEixist)">
                لديك هذه الايام بالفعل
            </v-card-title>
            <v-card-text>
                <!-- <h2>{{ absenceEixist }}</h2> -->
                <div v-if="Array.isArray(absenceEixist)">
                    <h2 v-for="(absence) in absenceEixist">{{ absence }}</h2>
                </div>
                <div v-else>{{ absenceEixist }}</div>
            </v-card-text>
            <v-card-actions>
                <v-btn color="primary" variant="text" @click="dialog2 = false">
                    اغلاق
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <!-- <v-dialog v-model="dialog5" width="100%">
        <v-card>
            <v-card-title>
                print Log
            </v-card-title>
            <v-card-text>
                {{ store.printLog }}
            </v-card-text>
            <v-card-actions>
                <v-btn color="primary" variant="text" @click="dialog5 = false">
                    اغلاق
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog> -->
</template>
<script setup>
import axios from "axios";
import { usemainStore } from "../../store/mainStore";
const store = usemainStore();
import { onMounted, ref } from "vue";
const dialog = ref(false);
const activeRest = ref([]);
const dialog2 = ref(false);
// const dialog5 = ref(false);
const absenceEixist = ref('');
const dailyFife = ref(false);
const addRequest = ref({});
const typeRequest = ref([
    { "en": "Regular", "ar": "اعتيادية" },
    { "en": "Rest allowance", "ar": " بدل راحة " },
    { "en": "Casual", "ar": "عارضة" },
    { "en": "Umrah leave", "ar": "إجازة عمرة" },
    { "en": "Recruitment", "ar": "تجنيد" },
    { "en": "Maternity leave", "ar": "إجازة وضع" },
    { "en": "Compensatory leave", "ar": "إجازة تعويضية" },
    { "en": "Other", "ar": "أخرى" }
]
);
onMounted(() => {
    store.getAbsences().then(() => {
        const restallowance = ref(store.restallowance);
        if (restallowance.value) {
            const filteractiveRest = ref(restallowance.value.filter(obj => obj.state == 1 || obj.state == 5));
            activeRest.value = filteractiveRest.value;
        }
    });
});

function saveRequest() {
    addRequest.value.dailyFife = dailyFife.value;
    if (addRequest.value.Type == 'Rest allowance') {
        addRequest.value.end_date = addRequest.value.start_date;
    }
    if (!addRequest.value.description) {
        if (addRequest.value.Type == 'Rest allowance') {
            // البحث عن الكائن الذي يحمل الـ ID المحدد
            let targetObject = store.restallowance.find(obj => obj.id === addRequest.value.rest_id);
            // الوصول إلى وصف الكائن
            addRequest.value.description = targetObject.description;
        } else {
            addRequest.value.description = 'بدون وصف';
        }
    }
    if (!addRequest.value.Type) {
        addRequest.value.Type = typeRequest.value[0].en;
    }
    axios
        .post(`absence`, addRequest.value)
        .then((re) => {
            store.getAbsences();
            addRequest.value = ref({});
            dialog.value = false;
        })
        .catch((er) => {
            // console.log(er.response.data);
            if (Array.isArray(er.response.data)) {
                absenceEixist.value = er.response.data;
            } else {
                absenceEixist.value = ' حدث خطا اعد المحاولة';
            }
            dialog2.value = true;
        });
}
function setField() {
    addRequest.value.start_date = store.formatDate(new Date());
    addRequest.value.end_date = store.formatDate(new Date());
}

// اعتيادية	Regular
//  بدل راحة	Rest allowance
//  عارضة	Casual
//  اجازة عمرة	Umrah leave
//  تجنيد	Recruitment
//  اجازة وضع	Maternity leave
//  اجازة تعويضية	Compensatory leave
//  اخري	Other
// const location = ref(null);

</script>
<style lang="scss">
.v-messages__message {
    padding: 13px;
    padding-top: 3px;
    color: red;
}

.non-clickable {
    pointer-events: none !important;
    cursor: not-allowed !important;
}
</style>
