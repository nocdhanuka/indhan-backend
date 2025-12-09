package com.dhanuka.indhan.Activity.misAndReports


import android.content.Context
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.dhanuka.indhan.Activity.misAndReports.model.ReportsListData
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.layout_reports_list_item.view.*

class MisDataListAdapter(
    val context: Context,
    val subMenuList: List<ReportsListData>
) : RecyclerView.Adapter<MisDataListAdapter.ViewHolder>() {

    private lateinit var clickListener: SubMenuClickListener

    inner class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {
            view.setOnClickListener { view ->
                if ((subMenuList != null) && (subMenuList.size > absoluteAdapterPosition) && (absoluteAdapterPosition != -1)) {
                    clickListener.onClick(view, subMenuList[absoluteAdapterPosition])
                }
            }
        }
    }

    fun setClickListener(itemClickListener: SubMenuClickListener) {
        this.clickListener = itemClickListener
    }

    override fun onCreateViewHolder(
        parent: ViewGroup,
        viewType: Int
    ): MisDataListAdapter.ViewHolder {
        val inflater =
            LayoutInflater.from(parent.context)
                .inflate(R.layout.layout_reports_list_item, parent, false)
        return ViewHolder(inflater)
    }

    override fun onBindViewHolder(holder: MisDataListAdapter.ViewHolder, position: Int) {
        holder.itemView.tvReportName.text = subMenuList[position].report_name
        if (subMenuList[position].month != 0) {
            holder.itemView.tvMonth.text =
                "${Utility.getMonth(subMenuList[position].month - 1)}, ${subMenuList[position].year}"
        }
    }

    override fun getItemCount(): Int {
        return subMenuList.size
    }

    interface SubMenuClickListener {
        fun onClick(view: View, subMenu: ReportsListData)
    }
}