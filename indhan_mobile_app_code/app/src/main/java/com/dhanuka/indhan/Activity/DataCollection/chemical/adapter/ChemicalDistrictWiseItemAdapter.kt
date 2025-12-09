package com.dhanuka.indhan.Activity.DataCollection.chemical.adapter

import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.Activity.DataCollection.chemical.ChemicalViewDistrictWiseActivity
import com.dhanuka.indhan.Model.ChemicalDistrictWiseDataList
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.item_chemical_district_wise.view.*

class ChemicalDistrictWiseItemAdapter(
    chemicalDataDistrictWise: ArrayList<ChemicalDistrictWiseDataList.Data>,
    activity: ChemicalViewDistrictWiseActivity
) :
    RecyclerView.Adapter<ChemicalDistrictWiseItemAdapter.MyViewHolder>() {

    private var listItem = ArrayList<ChemicalDistrictWiseDataList.Data>()
    private var listener: ChemicalDistrictWiseAdapterListener

    init {
        this.listItem = chemicalDataDistrictWise
        this.listener = activity
    }

    class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {

    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): MyViewHolder {
        val itemView = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_chemical_district_wise, parent, false)
        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(holder: MyViewHolder, position: Int) {
//        holder.itemView.txtDistrictName.text = listItem[holder.adapterPosition].district
//        holder.itemView.txtTerritoryName.text = listItem[holder.adapterPosition].state
        holder.itemView.txtClassName.text = listItem[holder.adapterPosition].classname
        holder.itemView.txtFormulationName.text = listItem[holder.adapterPosition].formulationname
//        holder.itemView.txtBrandName.text = listItem[holder.adapterPosition].brand

        holder.itemView.txtVolumeName.text = listItem[holder.adapterPosition].brand
        if (listItem[holder.adapterPosition].remarks.toString() != "test") {
            holder.itemView.txtRemarks.visibility = View.VISIBLE
            holder.itemView.txtRemarks.text =
                "Remarks - ${listItem[holder.adapterPosition].remarks}"
        } else {
            holder.itemView.txtRemarks.visibility = View.GONE
            holder.itemView.txtRemarks.text = ""
        }
        holder.itemView.txtVolumeValue.text = "${listItem[holder.adapterPosition].volume} Kg / Ltr"
//        listItem[holder.adapterPosition].nrv?.let {
//            if (!TextUtils.isEmpty(it)) {
//                if (!TextUtils.isEmpty(listItem[holder.adapterPosition].volume)) {
//                    val volume = it.toDouble()
//                        .times(listItem[holder.adapterPosition].volume.toString().toDouble())
//
//                        "Value - ₹ ${String.format(" % .1f", volume)}"
//                } else {
//                    holder.itemView.txtVolumeValue.text = " 0"
//                }
//            }
//        }
//        holder.itemView.txtEditDistrictWiseRecord.setOnClickListener {
//            listener.onItemSelected(listItem[holder.adapterPosition])
//        }
    }

    override fun getItemCount(): Int = listItem.size

    interface ChemicalDistrictWiseAdapterListener {
        fun onItemSelected(model: ChemicalDistrictWiseDataList.Data)
    }
}