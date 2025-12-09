package com.dhanuka.indhan.PlanSyllabusForBdo.Adapter

import android.app.Activity
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.R
import com.dhanuka.indhan.SyllabusHistory.Model.ProdListForSyllabusHistory
import kotlinx.android.synthetic.main.layout_syllabus_bdo_list.view.*


class HistoryProductFilteredAdapter(
    homeCategoryRecordResponseModelArrayList: ArrayList<ProdListForSyllabusHistory>,
    private val mActivityContext: Activity
) : RecyclerView.Adapter<HistoryProductFilteredAdapter.MyViewHolder>() {

    private val filteredData: ArrayList<ProdListForSyllabusHistory>
    lateinit var onclick: onClickProductListener

    init {
        this.filteredData = homeCategoryRecordResponseModelArrayList
    }

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {
            view.setOnClickListener { view -> onclick.onProductSelected(filteredData[adapterPosition]) }
        }
    }

    fun setOnclickListener(onclick: onClickProductListener) {
        this.onclick = onclick
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): MyViewHolder {
        val itemView = LayoutInflater.from(parent.context)
            .inflate(R.layout.layout_syllabus_bdo_list, parent, false)
        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(holder: MyViewHolder, position: Int) {
        val plan = filteredData[position]

        holder.itemView.bdo_name_tv.text = plan.productName
        holder.itemView.no_of_days_tv.text =
            "No of Activities  - " + plan.noOfDays.toString() + ", No. of BDO - " + plan.noOfBDO.toString()
        if (plan.submittedNo_ifDays > 0 && plan.submittednoOfBDO > 0) {
            holder.itemView.submit_no_of_days_tv.text =
                "Submitted : No of Activities  - " + plan.submittedNo_ifDays.toString() + ", No. of BDO - " + plan.submittednoOfBDO.toString()
            holder.itemView.submit_no_of_days_tv.visibility = View.VISIBLE
        } else {
            holder.itemView.submit_no_of_days_tv.visibility = View.GONE
        }
    }

    override fun getItemCount(): Int {
        return filteredData.size
    }

    interface onClickProductListener {
        fun onProductSelected(product_id: ProdListForSyllabusHistory)
    }
}
