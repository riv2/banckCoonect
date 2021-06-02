<template>
  <div @drop="_drop" @dragenter="_suppress" @dragover="_suppress">
    <input type="file" class="form-control" @change="_change" />
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th v-for="c in cols" :key="c.key">{{ c.name }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(r, key) in data" :key="key">
            <td>
                <input type="text" :name="'order[' + key + '][order_id]'" :value="r[0]" class="form-control form-control-sm">
            </td>
            <td>
                <input type="number" min="0" :name="'order[' + key + '][offer_id]'" :value="r[1]" class="form-control form-control-sm">
            </td>
            <td>
                <input type="number" min="0" :name="'order[' + key + '][partner_id]'" :value="r[2]" class="form-control form-control-sm">
            </td>
            <td>
                <input type="number" min="0" :name="'order[' + key + '][link_id]'" :value="r[3]" class="form-control form-control-sm">
            </td>
            <td>
                <input type="number" min="0" step="0.01" :name="'order[' + key + '][gross_amount]'" :value="r[4]" class="form-control form-control-sm">
            </td>
            <td>
                <input type="datetime-local" :name="'order[' + key + '][datetime]'" :value="_parseDate(r[5])" class="form-control form-control-sm">
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <button type="submit" class="btn btn-primary">ok</button>
  </div>
</template>

<script>
import XLSX from "xlsx";

var _SheetJSFT = ["xlsx", "xls", "csv"]
  .map(function (x) {
    return "." + x;
  })
  .join(",");

const make_cols = (refstr) =>
  Array(XLSX.utils.decode_range(refstr).e.c + 1)
    .fill(0)
    .map((x, i) => ({ name: XLSX.utils.encode_col(i), key: i }));

export default {
  data: function () {
    return {
      data: [["tst-1234567890", "10", "4", "6", "10.00", "44140"]],
      cols: [
        {
            name: "ID заказа",
            field: "order_id",
            key: 0
        },
        {
            name: "Оффер",
            field: "offer_id",
            type: 'number',
            key: 1
        },
        {
            name: "Партнер",
            field: "partner_id",
            type: 'number',
            key: 2
        },
        {
            name: "ID ссылки",
            field: "link_id",
            type: 'number',
            key: 3
        },
        {
            name: "Сумма заказа",
            field: "gross_amount",
            key: 4
        },
        {
            name: "Дата заказа",
            field: "datetime",
            type: 'text',
            key: 5
        },
      ],
      SheetJSFT: _SheetJSFT,
    };
  },
  methods: {
    _suppress(evt) {
      evt.stopPropagation();
      evt.preventDefault();
    },
    _drop(evt) {
      evt.stopPropagation();
      evt.preventDefault();
      const files = evt.dataTransfer.files;
      if (files && files[0]) this._file(files[0]);
    },
    _change(evt) {
      const files = evt.target.files;
      if (files && files[0]) this._file(files[0]);
    },

    _parseDate(date) {
        return new Date((date - (25567 + 1))*86400*1000).toISOString().slice(0,16);
    },
    _file(file) {
      /* Boilerplate to set up FileReader */
      const reader = new FileReader();
      reader.onload = (e) => {
        /* Parse data */
        const bstr = e.target.result;
        const wb = XLSX.read(bstr, { type: "binary" });
        /* Get first worksheet */
        const wsname = wb.SheetNames[0];
        const ws = wb.Sheets[wsname];

        /* generate HTML */
        /* Convert array of arrays */
        const data = XLSX.utils.sheet_to_json(ws, {
            header: 1,
        });
        /* Update state */
        var cols = data.shift();
        console.log(data[1]);
        this.data = data;
      };
      reader.readAsBinaryString(file);
    },
  },
};
</script>
