package com.dhanuka.indhan.PlanSyllabusForBdo.Adapter

import android.app.Activity
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.R
import com.dhanuka.indhan.SyllabusHistory.Model.BDListForSyllabusModel
import kotlinx.android.synthetic.main.layout_syllabus_bdo_list.view.*


class HistoryBdoFilteredAdapter(
    homeCategoryRecordResponseModelArrayList: ArrayList<BDListForSyllabusModel>,
    private val mActivityContext: Activity
) : RecyclerView.Adapter<HistoryBdoFilteredAdapter.MyViewHolder>() {

    private val filteredData: ArrayList<BDListForSyllabusModel>
    lateinit var onclick: onClickBdoListener

    init {
        this.filteredData = homeCategoryRecordResponseModelArrayList
    }

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {
            view.setOnClickListener { view -> onclick.onBdoSelected(filteredData[adapterPosition]) }
        }
    }

    fun setOnclickListener(onclick: onClickBdoListener) {
        this.onclick = onclick
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): MyViewHolder {
        val itemView = LayoutInflater.from(parent.context)
            .inflate(R.layout.layout_syllabus_bdo_list, parent, false)
        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(holder: MyViewHolder, position: Int) {
        val plan = filteredData[position]
        holder.itemView.bdo_name_tv.text = plan.bdo_name
        holder.itemView.no_of_days_tv.text = "No of Activities  - " + plan.noOdDays.toString()
        if (plan.submitted_noOfDays > 0) {
            holder.itemView.submit_no_of_days_tv.text =
                "Submitted : No of Activities - " + plan.submitted_noOfDays.toString()
            holder.itemView.submit_no_of_days_tv.visibility = View.VISIBLE
        } else {
            holder.itemView.submit_no_of_days_tv.visibility = View.GONE

        }
    }

    override fun getItemCount(): Int {
        return filteredData.size
    }

    interface onClickBdoListener {
        fun onBdoSelected(bdo_id: BDListForSyllabusModel)
    }
}
