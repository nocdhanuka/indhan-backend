package com.dhanuka.indhan.Reports.supervisor_report

import android.content.Context
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.ViewGroup
import com.dhanuka.indhan.R
import com.dhanuka.indhan.databinding.ItemDemosBinding


class DemosAdapter(var context: Context, var list: List<SupervisorReportsBean.Data.Demolist>) :
    RecyclerView.Adapter<DemosAdapter.ViewHolder>() {

    var layoutInflater = LayoutInflater.from(context)
    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding: ItemDemosBinding =
            DataBindingUtil.inflate(layoutInflater, R.layout.item_demos, parent, false)
        return ViewHolder(binding)
    }

    override fun getItemCount(): Int {
        return list.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {

        holder.setData(position)


    }


    inner class ViewHolder(var view: ItemDemosBinding) : RecyclerView.ViewHolder(view.root) {
        // Holds the TextView that will add each animal to
        fun setData(position: Int) {
            view.txtName.text = list[position].demoCode
            view.txtProductName.text = list[position].product
            view.txtFarmerName.text = list[position].farmerName

        }


    }


}