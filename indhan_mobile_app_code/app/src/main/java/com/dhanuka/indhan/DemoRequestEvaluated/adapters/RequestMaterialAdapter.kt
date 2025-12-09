package com.dhanuka.indhan.DemoRequestEvaluated.adapters

import android.content.Context
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.core.content.ContextCompat
import androidx.recyclerview.widget.RecyclerView
import com.dhanuka.indhan.DemoRequestEvaluated.models.RequestedMaterialResponseData
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.layout_requested_material_item.view.*

//#ff8921
//#1aaf56
class RequestMaterialAdapter(
    var listOfData: ArrayList<RequestedMaterialResponseData>,
    var myDemoRequestActivity: Context
) :
    RecyclerView.Adapter<RequestMaterialAdapter.ViewHolder>() {
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
                .inflate(R.layout.layout_requested_material_item, parent, false)
        return ViewHolder(inflater)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.itemView.tvCropData.text = "- ".plus(listOfData[position].crop_name)
        holder.itemView.tvProductData.text =  "- ".plus(listOfData[position].product_name)
        holder.itemView.tvRequestedQtyData.text =
            "- ".plus(listOfData[position].requested_units.plus(" Kg/Ltr"))
        holder.itemView.tvEstimatedDemoData.text =  "- ".plus(listOfData[position].expected_demos)
        holder.itemView.tvGoDownData.text =  "- ".plus(listOfData[position].godown_name)
        holder.itemView.tvNeedByData.text =  "- ".plus(listOfData[position].need_by_date)
        when {
            listOfData[position].rsm_approval_status == 0 -> {
                holder.itemView.labelStatus.setBackgroundColor(
                    ContextCompat.getColor(myDemoRequestActivity, R.color.red)
                )
                holder.itemView.labelStatus.text = "Pending RSM Approval"
            }
            listOfData[position].admin_approval_status == 0 -> {
                holder.itemView.labelStatus.setBackgroundColor(
                    ContextCompat.getColor(myDemoRequestActivity, R.color.orange_demo)
                )
                holder.itemView.labelStatus.text = "Pending Admin Approval"
            }
            listOfData[position].dispatch_status == 0 -> {
                holder.itemView.labelStatus.setBackgroundColor(
                    ContextCompat.getColor(myDemoRequestActivity, R.color.magenta_btn)
                )
                holder.itemView.labelStatus.text = "Yet To Dispatch"
            }
            listOfData[position].receiving_status == 0 -> {
                holder.itemView.labelStatus.setBackgroundColor(
                    ContextCompat.getColor(myDemoRequestActivity, R.color.light_green)
                )
                holder.itemView.labelStatus.text = "Dispatched"
            }
            else -> {
                holder.itemView.labelStatus.setBackgroundColor(
                    ContextCompat.getColor(myDemoRequestActivity, R.color.green_btn)
                )
                holder.itemView.labelStatus.text = "Received"
            }
        }
    }


    override fun getItemCount(): Int {
        return listOfData.size
    }

    interface RequestedMaterialClick {
        fun onClickRequestedMaterial(data: RequestedMaterialResponseData)
    }

}