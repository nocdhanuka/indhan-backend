package com.dhanuka.indhan.PlanSyllabusForBdo.Dialog

import android.app.Activity
import android.app.AlertDialog
import android.text.Editable
import android.text.TextUtils
import android.text.TextWatcher
import android.view.View
import android.widget.AdapterView
import android.widget.EditText
import android.widget.ListView
import android.widget.TextView
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.OnSpinerItemClick

/**
 * Created by Shivam on 3/20/2018.
 */

class NumberSpinnerSelectable {
    internal val items: ArrayList<ColorModel>
    internal var context: Activity
    internal var dTitle: String
    internal var closeTitle = "Close"
    internal lateinit var onSpinerItemClick: OnSpinerItemClick
    internal lateinit var alertDialog: AlertDialog
    internal var pos: Int = 0
    internal var style: Int = 0
    internal var isEnable = false


    constructor(
        activity: Activity,
        items: ArrayList<ColorModel>,
        dialogTitle: String
    ) {
        this.items = items
        this.context = activity
        this.dTitle = dialogTitle
    }


    fun bindOnSpinerListener(onSpinerItemClick1: OnSpinerItemClick) {
        this.onSpinerItemClick = onSpinerItemClick1
    }

    fun showSpinerDialog() {
        val itemstr = ArrayList<ColorModel>()
        for (i in items.indices) {
            val data = ColorModel()
            data.color = items[i].color
            data.name = items[i].name
            itemstr.add(data)
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
//        val adapter = ArrayAdapter(context, R.layout.items_view, itemstr)
        val adapter = ColorAdapter(context, itemstr)
        listView.adapter = adapter
        adb.setView(v)
        alertDialog = adb.create()
        alertDialog.window!!.attributes.windowAnimations =
            style//R.style.DialogAnimations_SmileWindow;
        alertDialog.setCancelable(false)

        listView.onItemClickListener = AdapterView.OnItemClickListener { adapterView, view, i, l ->
            val t = view.findViewById<View>(R.id.text1) as TextView
            for (j in items.indices) {
                if (t.text.toString().equals(items[j].name, ignoreCase = true)) {
                    pos = j
                }
            }
            onSpinerItemClick.onClick(t.text.toString(), pos)
            isEnable = false
            alertDialog.dismiss()

        }

        searchBox.addTextChangedListener(object : TextWatcher {
            override fun beforeTextChanged(charSequence: CharSequence, i: Int, i1: Int, i2: Int) {

            }

            override fun onTextChanged(charSequence: CharSequence, i: Int, i1: Int, i2: Int) {

            }

            override fun afterTextChanged(editable: Editable) {
                if (!TextUtils.isEmpty(editable.toString().trim())) {
                    adapter.getFilter().filter(editable.toString().trim())
                } else {
                    adapter.getFilter().filter("")
                }
            }
        })

        rippleViewClose.setOnClickListener {
            isEnable = false
            alertDialog.dismiss()
        }
        alertDialog.show()
    }
}
