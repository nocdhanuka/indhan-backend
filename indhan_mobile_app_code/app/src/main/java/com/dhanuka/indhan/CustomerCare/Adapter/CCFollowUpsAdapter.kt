package com.dhanuka.indhan.PlanSyllabusForBdo.Adapter

import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.CustomerCare.Model.CCQueriesModel
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.layout_cc_row.view.*

//

class CCFollowUpsAdapter(homeCategoryRecordResponseModelArrayList: ArrayList<CCQueriesModel.CustomerDatum>) :
    RecyclerView.Adapter<CCFollowUpsAdapter.MyViewHolder>() {
    private var filteredData: ArrayList<CCQueriesModel.CustomerDatum>
    var record_bck: ArrayList<CCQueriesModel.CustomerDatum>? = ArrayList()

    lateinit var onclickListener: CCListClickListener

    init {
        this.filteredData = homeCategoryRecordResponseModelArrayList
        this.record_bck?.addAll(homeCategoryRecordResponseModelArrayList)
    }

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {
            view.setOnClickListener { view -> onclickListener.onSelected(filteredData[adapterPosition]) }
        }
    }

    fun setClickListener(onclickListener: CCListClickListener) {
        this.onclickListener = onclickListener
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): MyViewHolder {
        val itemView =
            LayoutInflater.from(parent.context).inflate(R.layout.layout_cc_row, parent, false)
        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(holder: MyViewHolder, position: Int) {
        val plan = filteredData[position]
        showDate(holder, position)
        holder.itemView.ref_no.text = "Reference No. #" + plan.tokenNumber
        holder.itemView.time_tv.text = plan.time
//        if (plan.product.isNullOrEmpty() || plan.typeOfCall.isNullOrEmpty())
//            holder.itemView.prod_type_tv.text = plan.product + " | " + plan.typeOfCall
        val text = StringBuilder()
        if (plan.product.isNotEmpty() && plan.product != "NA")
            text.append(plan.product + " | ")
        if (plan.typeOfCall.isNotEmpty())
            text.append(plan.typeOfCall)
        holder.itemView.prod_type_tv.text = text.toString()
        if (plan.status.equals("closed", true)) {
            holder.itemView.status_tv.visibility = View.GONE
        } else {
            holder.itemView.status_tv.visibility = View.VISIBLE
            holder.itemView.status_tv.text = plan.status.toUpperCase()
        }
        /* else if(plan.typeOfCall.isNullOrEmpty())
         holder.itemView.prod_type_tv.text=plan.product +" | "+ plan.typeOfCall
         else
             holder.itemView.prod_type_tv.text=plan.product +" | "+ plan.typeOfCall*/
        holder.itemView.remark_tv.text = plan.remark

    }

    override fun getItemCount(): Int {
        return filteredData.size
    }

    interface CCListClickListener {
        fun onSelected(model: CCQueriesModel.CustomerDatum)
    }


    private fun showDate(holder: MyViewHolder, position: Int) {
        val plan = filteredData[position]
        var dates = plan.date_str
        /*if (plan.date_str!=null)
            dates = Utility.getMonth(plan.month-1).toUpperCase()*/
        if (position == 0) {
            holder.itemView.date_tv.text = dates
            holder.itemView.date_tv.visibility = View.VISIBLE
        } else {
            if (plan.date_str.equals(filteredData[position - 1].date_str)) {
                holder.itemView.date_tv.visibility = View.GONE
            } else {
                holder.itemView.date_tv.visibility = View.VISIBLE
                holder.itemView.date_tv.text = dates
            }
        }
    }

}
