package com.dhanuka.indhan.PlanSyllabusForBdo.Dialog

import android.app.Activity
import android.app.AlertDialog
import android.text.Editable
import android.text.TextUtils
import android.text.TextWatcher
import android.view.View
import android.widget.EditText
import android.widget.ListView
import android.widget.TextView
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.OnSpinnerMultipleItemClick

/**
 * Created by Shivam on 3/20/2018.
 */

class AllMultipleNumberSpinner(
    activity: Activity,
    internal var items: ArrayList<MultipleModel>,
    dialogTitle: String
) : AllMultipleSelectionAdapter.onOperationMultiple {
    private var selectedItems = ArrayList<MultipleModel>()
    internal var context: Activity = activity
    internal var dTitle: String = dialogTitle
    internal var closeTitle = "Close"
    internal lateinit var onSpinerItemClick: OnSpinnerMultipleItemClick
    internal lateinit var alertDialog: AlertDialog
    internal var pos: Int = 0
    internal var style: Int = 0
    internal var isEnable = false


    fun bindOnSpinerListener(onSpinerItemClick1: OnSpinnerMultipleItemClick) {
        this.onSpinerItemClick = onSpinerItemClick1
    }

    fun showSpinerDialog(idsList: ArrayList<String>) {
        selectedItems.clear()
        val itemstr = ArrayList<MultipleModel>()
        for (i in items.indices) {
            items[i].selected = idsList.contains(items[i].id)
            if (items[i].selected) {
                selectedItems.add(items[i])
            }
            itemstr.add(items[i])
        }

        isEnable = true
        val adb = AlertDialog.Builder(context)
        val v = context.layoutInflater.inflate(R.layout.multiple_dialog_layout, null)
        val rippleViewClose = v.findViewById<View>(R.id.close) as TextView
        val rippleViewDone = v.findViewById<View>(R.id.done) as TextView
        val title = v.findViewById<View>(R.id.spinerTitle) as TextView
        rippleViewClose.text = closeTitle
        title.text = dTitle
        val listView = v.findViewById<View>(R.id.list) as ListView
        val searchBox = v.findViewById<View>(R.id.searchBox) as EditText
        searchBox.visibility = View.GONE
        val adapter = AllMultipleSelectionAdapter(context, itemstr, this@AllMultipleNumberSpinner)
        listView.adapter = adapter
        adb.setView(v)
        alertDialog = adb.create()
        alertDialog.window!!.attributes.windowAnimations = style
        alertDialog.setCancelable(false)

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

        rippleViewDone.setOnClickListener {
            onSpinerItemClick.onClick(selectedItems)
            isEnable = false
            alertDialog.dismiss()
        }
        alertDialog.show()
    }

    override fun onCheckBoxClick(data: MultipleModel) {
        if (data.selected) {
            if (!selectedItems.contains(data))
                selectedItems.add(data)
        } else {
            if (selectedItems.contains(data))
                selectedItems.remove(data)
        }
    }

    override fun onCheckAllBoxClick(data: java.util.ArrayList<MultipleModel>) {
        selectedItems.clear()
        for ((index) in data.withIndex()) {
            if (data[index].name.equals("Select All", ignoreCase = true)) {
                if (data[index].selected) {
                    items.forEach() {
                        it.selected = true;
                    }
                    selectedItems.clear()
                    selectedItems.addAll(items)
                } else {
                    items.forEach() {
                        it.selected = false;
                    }
                    selectedItems.clear()
                }
            } else {
                break
            }
        }
    }
}
