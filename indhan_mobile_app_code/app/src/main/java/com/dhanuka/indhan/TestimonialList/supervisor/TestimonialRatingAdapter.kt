package com.dhanuka.indhan.TestimonialList.supervisor

import android.content.Context
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.ViewGroup
import com.dhanuka.indhan.R
import com.dhanuka.indhan.databinding.ItemTestimonialSupervisorRatingBinding


class TestimonialRatingAdapter(
    var context: Context,
    var list: MutableList<SupervisorTestimonialBean.Data.TestimonialData.TestimonialRating>
) : RecyclerView.Adapter<TestimonialRatingAdapter.ViewHolder>() {

    var layoutInflater = LayoutInflater.from(context)
    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding: ItemTestimonialSupervisorRatingBinding = DataBindingUtil.inflate(
            layoutInflater,
            R.layout.item_testimonial_supervisor_rating,
            parent,
            false
        )
        return ViewHolder(binding)
    }

    override fun getItemCount(): Int {
        return list.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {

        holder.setData(position)

    }


    inner class ViewHolder(var view: ItemTestimonialSupervisorRatingBinding) :
        RecyclerView.ViewHolder(view.root) {
        // Holds the TextView that will add each animal to
        fun setData(position: Int) {
//            view.txtTestimonialName=list[position]
            view.txtRatingName.text = list[position].supervisorName
            view.ratingBar.rating = list[position].rating.toFloat()


        }

    }


}