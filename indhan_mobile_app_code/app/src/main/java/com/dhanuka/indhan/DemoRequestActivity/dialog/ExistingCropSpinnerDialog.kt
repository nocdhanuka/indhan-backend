package com.dhanuka.indhan.Activity.NewTicketMVP

import android.app.Activity
import android.app.AlertDialog
import android.text.Editable
import android.text.TextWatcher
import android.view.View
import android.widget.*
import com.dhanuka.indhan.DemoRequestActivity.models.Crop
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.OnGetId
import com.dhanuka.indhan.util.OnSpinerItemClick
import java.util.*

/**
 * Created by Junaid on 3/06/2019.
 */

class ExistingCropSpinnerDialog {
    internal var items: ArrayList<Crop>
    internal var context: Activity
    internal var dTitle: String
    internal var closeTitle = "Close"
    internal lateinit var onSpinerItemClick: OnGetId
    internal lateinit var alertDialog: AlertDialog
    internal var pos: Int = 0
    internal var style: Int = 0
    internal var isEnable = false
    internal var productId = ""


    constructor(
        activity: Activity,
        items: ArrayList<Crop>,
        dialogTitle: String,
        onSpinerItemClick: OnGetId
    ) {
        this.items = items
        this.context = activity
        this.dTitle = dialogTitle
        this.onSpinerItemClick = onSpinerItemClick
    }

    fun updatelist(items: ArrayList<Crop>) {
        this.items = items
    }

    fun bindOnSpinerListener(onSpinerItemClick1: OnSpinerItemClick) {
        this.onSpinerItemClick = onSpinerItemClick
    }

    fun showSpinerDialog() {
        val itemstr = ArrayList<String>()
        for (i in items.indices) {
            itemstr.add(items[i].crop.toString())
        }
        isEnable = true
        val adb = AlertDialog.Builder(context)
        val v = context.layoutInflater.inflate(R.layout.dialog_layout, null)
        val rippleViewClose = v.findViewById<View>(R.id.close) as TextView
        val title = v.findViewById<View>(R.id.spinerTitle) as TextView
        rippleViewClose.text = closeTitle
        title.text = dTitle
        val listView = v.findViewById<View>(R.id.list) as ListView
        val searchBox = v.findViewById<View>(R.id.searchBox) as EditText
        val adapter = ArrayAdapter(context, R.layout.items_view, itemstr)
        listView.adapter = adapter
        adb.setView(v)
        alertDialog = adb.create()
        alertDialog.window!!.attributes.windowAnimations =
            style//R.style.DialogAnimations_SmileWindow;
        alertDialog.setCancelable(false)

        listView.onItemClickListener = AdapterView.OnItemClickListener { adapterView, view, i, l ->
            val t = view.findViewById<View>(R.id.text1) as TextView
            for (j in items.indices) {
                if (t.text.toString().equals(items[j].crop!!.toString(), ignoreCase = true)) {
                    pos = j
                    productId = items[j].crop_id
                }
            }
            onSpinerItemClick.onClick(t.text.toString(), pos, productId)
            isEnable = false
            alertDialog.dismiss()
        }

        searchBox.addTextChangedListener(object : TextWatcher {
            override fun beforeTextChanged(charSequence: CharSequence, i: Int, i1: Int, i2: Int) {

            }

            override fun onTextChanged(charSequence: CharSequence, i: Int, i1: Int, i2: Int) {

            }

            override fun afterTextChanged(editable: Editable) {
                adapter.filter.filter(searchBox.text.toString())
            }
        })

        rippleViewClose.setOnClickListener {
            isEnable = false
            alertDialog.dismiss()
        }
        alertDialog.show()
    }
}
