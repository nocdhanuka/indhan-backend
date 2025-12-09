package com.dhanuka.indhan.institutional.adapter.teaEstate

import android.app.Activity
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.dhanuka.indhan.R
import com.dhanuka.indhan.institutional.model.teaEstate.TeaEstatePlanAtpModel
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.layout_tea_plan_atp_list.view.*

class TeaEstateAddAtpAdapter(
    private val addPlanAtpList: ArrayList<TeaEstatePlanAtpModel>,
    private val mActivityContext: Activity
) : RecyclerView.Adapter<TeaEstateAddAtpAdapter.MyViewHolder>() {

    private val filteredData: ArrayList<TeaEstatePlanAtpModel> = addPlanAtpList

    private lateinit var onLongClickListener: OrClickListener

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {

            view.setOnLongClickListener {
                onLongClickListener.onLongClick(
                    view,
                    filteredData[absoluteAdapterPosition],
                    absoluteAdapterPosition
                )
                true
            }

            view.imgDelete.setOnClickListener {
                onLongClickListener.onLongClick(
                    view,
                    filteredData[absoluteAdapterPosition],
                    absoluteAdapterPosition
                )
            }
        }
    }

    fun setLongClickListener(onLongClickListener: OrClickListener) {
        this.onLongClickListener = onLongClickListener
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): MyViewHolder {
        val itemView = LayoutInflater.from(parent.context)
            .inflate(R.layout.layout_tea_plan_atp_list, parent, false)
        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(holder: MyViewHolder, position: Int) {
        val plan = filteredData[position]
        holder.itemView.estate_name.text = ""
        val nameForPlanned = "Planned For - ".plus(plan.flName).plus(" ").plus("Planned for ECN - ${plan.flEcn}")

        holder.itemView.fl_name.text = nameForPlanned
        val textParam = "Category - ".plus(if (plan.companyCategory == "1") "A" else if (plan.companyCategory == "2") "B" else "C")

        textParam.plus("\nFor Fl - ${if (plan.manager == "1") "Yes" else "No"}")
        holder.itemView.details.text = textParam

        showDate(holder, position)
    }

    override fun getItemCount(): Int = filteredData.size

    private fun showDate(holder: MyViewHolder, position: Int) {
        var dates = ""
        val plan = filteredData[position]
        dates = if (plan.dateTime.contains("T"))
            Utility.getDateForerrorView(plan.dateTime)
        else
            Utility.getDateForView(plan.dateTime)

        if (position == 0) {
            holder.itemView.datetv.text = dates
            holder.itemView.datetv.visibility = View.VISIBLE

        } else {
            if (plan.dateTime == filteredData[position - 1].dateTime) {
                holder.itemView.datetv.visibility = View.GONE
            } else {
                holder.itemView.datetv.visibility = View.VISIBLE

                holder.itemView.datetv.text = dates
            }

        }
    }

    interface OrClickListener {
        fun onLongClick(view: View, atpDbModel: TeaEstatePlanAtpModel, position: Int)
    }

}
