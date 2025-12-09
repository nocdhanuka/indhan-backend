package com.dhanuka.indhan.institutional.adapter

import android.content.Context
import android.graphics.Color
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.dhanuka.indhan.R
import com.dhanuka.indhan.institutional.model.DistributorListData
import kotlinx.android.synthetic.main.item_distributor_data.view.*

class DistributorListAdapter(
    private val context: Context,
    private val arrayListOfDistributors: ArrayList<DistributorListData>
) : RecyclerView.Adapter<DistributorListAdapter.ViewHolder>() {

    private lateinit var clickListener: OnClickViewDistributorItem

    fun setViewClickListener(itemClickListener: OnClickViewDistributorItem) {
        this.clickListener = itemClickListener
    }

    inner class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
    }

    override fun onCreateViewHolder(
        parent: ViewGroup,
        viewType: Int
    ): ViewHolder {
        val inflater =
            LayoutInflater.from(parent.context)
                .inflate(R.layout.item_distributor_data, parent, false)
        return ViewHolder(inflater)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        if (arrayListOfDistributors[position].is_visited == 2) {
            holder.itemView.txtDepartureVisit.visibility = View.GONE
            holder.itemView.txtArrivalVisit.visibility = View.GONE
        } else {
            holder.itemView.txtDepartureVisit.visibility = View.VISIBLE
            holder.itemView.txtArrivalVisit.visibility = View.VISIBLE

            if (arrayListOfDistributors[position].is_visited == 0) {
                holder.itemView.txtArrivalVisit.isEnabled = true
                holder.itemView.txtDepartureVisit.isEnabled = false
                holder.itemView.txtArrivalVisit.setTextColor(Color.parseColor("#FFFFFFFF"))
                holder.itemView.txtArrivalVisit.setBackgroundColor(Color.parseColor("#FF1AAF56"))
                holder.itemView.txtDepartureVisit.setBackgroundColor(Color.parseColor("#FF9B9B9B"))
                holder.itemView.txtDepartureVisit.setTextColor(Color.parseColor("#FFFFFFFF"))
            } else {
                holder.itemView.txtArrivalVisit.isEnabled = false
                holder.itemView.txtDepartureVisit.isEnabled = true
                holder.itemView.txtArrivalVisit.setBackgroundColor(Color.parseColor("#FF9B9B9B"))
                holder.itemView.txtDepartureVisit.setBackgroundColor(Color.parseColor("#FF1AAF56"))
                holder.itemView.txtArrivalVisit.setTextColor(Color.parseColor("#FFFFFFFF"))
                holder.itemView.txtDepartureVisit.setTextColor(Color.parseColor("#FFFFFFFF"))
            }
        }

        holder.itemView.txtViewVisit.setOnClickListener {
            clickListener.onClickViewVisit(
                it,
                arrayListOfDistributors[holder.absoluteAdapterPosition]
            )
        }

        holder.itemView.txtArrivalVisit.setOnClickListener {
            clickListener.onClickArrivalView(
                holder.absoluteAdapterPosition,
                arrayListOfDistributors[holder.absoluteAdapterPosition]
            )
        }

        holder.itemView.txtDepartureVisit.setOnClickListener {
            clickListener.onClickDepartureView(
                holder.absoluteAdapterPosition,
                arrayListOfDistributors[holder.absoluteAdapterPosition]
            )
        }

        holder.itemView.tvDistributorName.text =
            "${arrayListOfDistributors[position].distributor_name}"

        holder.itemView.tvCompanyName.text =
            "${arrayListOfDistributors[position].distributor_firm_name}"

        holder.itemView.tvDistrictName.text =
            "${arrayListOfDistributors[position].district}"

        holder.itemView.tvTerritoryData.text =
            "${arrayListOfDistributors[position].territory}"

        holder.itemView.tvMarket.text =
            "${arrayListOfDistributors[position].market}"

        "${arrayListOfDistributors[position].mobile}".also {
            holder.itemView.tvPhoneData.text = it
        }
        "${arrayListOfDistributors[position].mobile2}".also {
            holder.itemView.tvPhoneData2.text = it
        }

        holder.itemView.tvAddress.text =
            "${arrayListOfDistributors[position].address}"

        holder.itemView.tvType.text =
            if (arrayListOfDistributors[position].is_distributor == 0) "Distributor" else "Advisor"
    }

    override fun getItemCount(): Int = arrayListOfDistributors.size

    interface OnClickViewDistributorItem {
        fun onClickViewVisit(view: View, subMenu: DistributorListData)
        fun onClickArrivalView(pos: Int, subMenu: DistributorListData)
        fun onClickDepartureView(pos: Int, subMenu: DistributorListData)
    }

}