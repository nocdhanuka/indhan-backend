package com.dhanuka.indhan.PlanATP.Adapter

import android.content.Context
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.ATPMenu.activity.OnClickListener
import com.dhanuka.indhan.R
import com.dhanuka.indhan.TestimonialList.filter.DDBean
import kotlinx.android.synthetic.main.item_dd.view.*

class DdSelectionAdapter(
    var context: Context,
    var ddList: MutableList<DDBean.Data>,
    var clickListener: OnClickListener
) : RecyclerView.Adapter<DdSelectionAdapter.ViewHolder>() {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view: View = LayoutInflater.from(context).inflate(R.layout.item_dd, parent, false)
        return ViewHolder(view)
    }

    override fun getItemCount(): Int {
        return ddList.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.view.txtDdName.text = ddList[position].name
        holder.view.setOnClickListener {
            clickListener.onClick(position)
        }
    }

    inner class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        var view: View

        init {
            this.view = view
        }
    }
}