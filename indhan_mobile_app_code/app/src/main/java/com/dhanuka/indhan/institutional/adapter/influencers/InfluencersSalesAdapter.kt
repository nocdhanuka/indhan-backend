package com.dhanuka.indhan.institutional.adapter.influencers

import android.content.Context
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.dhanuka.indhan.R
import com.dhanuka.indhan.institutional.model.influencers.InfluencersSalesData
import com.dhanuka.indhan.util.Currency
import kotlinx.android.synthetic.main.item_sales_report.view.*

class InfluencersSalesAdapter(
    var context: Context,
    var salesListData: ArrayList<InfluencersSalesData>
) : RecyclerView.Adapter<InfluencersSalesAdapter.ViewHolder>() {

    private lateinit var clickListener: OnClickItem

    fun setClickListener(itemClickListener: OnClickItem) {
        this.clickListener = itemClickListener
    }

    inner class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {
            view.setOnClickListener { view ->
                if ((salesListData != null) && (salesListData.size > absoluteAdapterPosition) && (absoluteAdapterPosition != -1)) {
                    clickListener.onClickView(view, salesListData[absoluteAdapterPosition])
                }
            }
        }
    }

    override fun onCreateViewHolder(
        parent: ViewGroup,
        viewType: Int
    ): ViewHolder {
        val inflater =
            LayoutInflater.from(parent.context)
                .inflate(R.layout.item_sales_report, parent, false)
        return ViewHolder(inflater)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.itemView.text_timeline_title.text =
            "Month - ".plus(salesListData[position].month.toString())
        holder.itemView.text_timeline_value.text =
            "Year - ".plus(salesListData[position].year.toString())
        holder.itemView.text_timeline_head.text = salesListData[position].influencer_name
        holder.itemView.text_timeline_date.text =
            "Total Sales - ".plus(salesListData[position].sales_figure.toString()).plus(
                "\nInWords - ${
                    Currency.convertToIndianCurrency(
                        salesListData[position].sales_figure.toString().trim()
                    )
                }"
            )
    }

    override fun getItemCount(): Int = salesListData.size

    interface OnClickItem {
        fun onClickView(view: View, subMenu: InfluencersSalesData)
    }

}