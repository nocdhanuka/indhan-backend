package com.dhanuka.indhan.PlanSyllabusForBdo.Adapter

import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.DataBase.TableDataModel.PlanAtpList
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.layout_bdo_plan_list.view.*


//

class BDOPlanListAdapter(homeCategoryRecordResponseModelArrayList: ArrayList<PlanAtpList>) :
    RecyclerView.Adapter<BDOPlanListAdapter.MyViewHolder>() {

    private val filteredData: ArrayList<PlanAtpList>
    lateinit var onclickListener: onClickListener

    init {
        this.filteredData = homeCategoryRecordResponseModelArrayList
    }

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {
            view.setOnClickListener { view -> onclickListener.onSelected(filteredData[adapterPosition]) }
        }
    }

    fun setClickListener(onclickListener: onClickListener) {

        this.onclickListener = onclickListener
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): MyViewHolder {
        val itemView = LayoutInflater.from(parent.context)
            .inflate(R.layout.layout_bdo_plan_list, parent, false)
        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(holder: MyViewHolder, position: Int) {
        val plan = filteredData[position]
        holder.itemView.bdo_name_tv.text = plan.name
        holder.itemView.no_of_days_tv.text = "No. of Activities -" + plan.activites.toString()
        showDate(holder, position)
    }

    override fun getItemCount(): Int {
        return filteredData.size
    }

    private fun showDate(holder: MyViewHolder, position: Int) {
        val plan = filteredData[position]
        var dates = ""
        if (plan.month != null)
            dates = Utility.getMonth(plan.month - 1).toUpperCase()
        if (position == 0) {
            holder.itemView.date_tv.text = dates
            holder.itemView.date_tv.visibility = View.VISIBLE
        } else {
            if (plan.month == filteredData[position - 1].month) {
                holder.itemView.date_tv.visibility = View.GONE
            } else {
                holder.itemView.date_tv.visibility = View.VISIBLE
                holder.itemView.date_tv.text = dates
            }
        }
    }

    interface onClickListener {
        fun onSelected(model: PlanAtpList)
    }

}
