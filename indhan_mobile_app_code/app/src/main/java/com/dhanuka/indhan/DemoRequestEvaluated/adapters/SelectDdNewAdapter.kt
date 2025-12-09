package com.dhanuka.indhan.DemoRequestEvaluated.adapters

import android.content.Context
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.CompoundButton
import com.dhanuka.indhan.R
import com.dhanuka.indhan.acpfarmers.ACPFIlterBean
import kotlinx.android.synthetic.main.item_select_demo_filter.view.*


class SelectDdNewAdapter(context: Context, ddList: ArrayList<ACPFIlterBean.Data.DD>) :
    RecyclerView.Adapter<SelectDdNewAdapter.ViewHolder>() {
    var context: Context
    var ddList: ArrayList<ACPFIlterBean.Data.DD>

    init {
        this.context = context
        this.ddList = ddList
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view: View =
            LayoutInflater.from(context).inflate(R.layout.item_select_demo_filter, parent, false)
        return ViewHolder(view)
    }

    override fun getItemCount(): Int {
        return ddList.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.view.txtDdName.text = ddList.get(position).name
        if (ddList.get(position).isChecked) {
            holder.view.chbDdName.isChecked = true
        } else {
            holder.view.chbDdName.isChecked = false

        }
        holder.view.chbDdName.setOnCheckedChangeListener { compoundButton: CompoundButton, isChecked: Boolean ->
            if (isChecked) {
                ddList.get(position).isChecked = true
            } else {
                ddList.get(position).isChecked = false

            }
        }
    }

    inner class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        var view: View

        init {
            this.view = view
        }
    }
}