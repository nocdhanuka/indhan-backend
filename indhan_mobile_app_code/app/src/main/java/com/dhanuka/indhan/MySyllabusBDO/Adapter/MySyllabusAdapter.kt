package com.dhanuka.indhan.PlanSyllabusForBdo.Adapter

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.dhanuka.indhan.DataBase.TableDataModel.MySyllabusDBModel
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.layout_my_syllabus.view.*
import java.util.*


class MySyllabusAdapter(homeCategoryRecordResponseModelArrayList: ArrayList<MySyllabusDBModel>) :
    RecyclerView.Adapter<MySyllabusAdapter.MyViewHolder>() {

    private val filteredData: ArrayList<MySyllabusDBModel> =
        homeCategoryRecordResponseModelArrayList
    lateinit var onclick: onClickSyllabusListener

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {
            view.setOnClickListener { onclick.onSyllabusSelected(filteredData[bindingAdapterPosition]) }
        }
    }

    fun setOnclickListener(onclick: onClickSyllabusListener) {
        this.onclick = onclick
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): MyViewHolder {
        val itemView =
            LayoutInflater.from(parent.context).inflate(R.layout.layout_my_syllabus, parent, false)
        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(holder: MyViewHolder, position: Int) {
        val plan = filteredData[position]
        holder.itemView.product_name.text = plan.product_name
        holder.itemView.days_tv.text =
            "Syllabus Assigned - " + plan.no_of_days.toString() + "     |      Planned ATP - " + plan.atp.toString()
        holder.itemView.txt_total_planned_atp.text = "No of DD - " + plan.no_of_dd
        holder.itemView.crop_name.text = plan.crop_name
        showDate(holder, position)
    }

    private fun showDate(holder: MyViewHolder, position: Int) {
        val plan = filteredData[position]
        var dates = ""
        if (plan.month != null)
            dates = Utility.getMonth(plan.month - 1).uppercase(Locale.getDefault())
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

    override fun getItemCount(): Int {
        return filteredData.size
    }

    interface onClickSyllabusListener {
        fun onSyllabusSelected(syllabus: MySyllabusDBModel)
    }
}
