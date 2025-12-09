package com.dhanuka.indhan.DemoRequestEvaluated.adapters

import android.content.Context
import android.text.TextUtils
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.dhanuka.indhan.DemoRequestEvaluated.models.UtilizationSummaryData
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.item_utilization_summary.view.*

class UtilizationSummaryAdapter(
    var listOfData: ArrayList<UtilizationSummaryData>,
    var myDemoRequestActivity: Context
) :
    RecyclerView.Adapter<UtilizationSummaryAdapter.ViewHolder>() {
    private lateinit var clickListener: RequestedMaterialClick

    inner class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {
            view.setOnClickListener { view ->
                if ((listOfData != null) && (listOfData.size > absoluteAdapterPosition) && (absoluteAdapterPosition != -1)) {
                    clickListener.onClickRequestedMaterial(listOfData[absoluteAdapterPosition])
                }
            }
        }
    }

    fun setClickListener(itemClickListener: RequestedMaterialClick) {
        this.clickListener = itemClickListener
    }

    override fun onCreateViewHolder(
        parent: ViewGroup,
        viewType: Int
    ): ViewHolder {
        val inflater =
            LayoutInflater.from(parent.context)
                .inflate(R.layout.item_utilization_summary, parent, false)
        return ViewHolder(inflater)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.itemView.tvCropData.text =  "- ".plus(listOfData[position].crop_name)
        holder.itemView.tvProductData.text =  "- ".plus(listOfData[position].product_name)
        holder.itemView.tvRequestedBy.text =
            if (!TextUtils.isEmpty(listOfData[position].s1_ecn_name)) "- ".plus(listOfData[position].s1_ecn_name) else "-"
        holder.itemView.tvDemoDoneData.text =  "- ".plus(listOfData[position].demo_done.toString())
        holder.itemView.tvReceivedMaterialData.text =  "- ".plus(listOfData[position].received_materials)
        holder.itemView.tvDemoAppliedData.text =  "- ".plus(listOfData[position].demo_applied.toString())
        holder.itemView.tvTentativeWasteData.text =  "- ".plus(listOfData[position].waste.toString())
    }


    override fun getItemCount(): Int {
        return listOfData.size
    }

    interface RequestedMaterialClick {
        fun onClickRequestedMaterial(data: UtilizationSummaryData)
    }

}