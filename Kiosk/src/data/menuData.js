export const menuData = {
  categories: [
    { id: 1, name: "Burgers", icon: "🍔" },
    { id: 2, name: "Sides", icon: "🍟" },
    { id: 3, name: "Drinks", icon: "🥤" },
    { id: 4, name: "Desserts", icon: "🍰" },
    { id: 5, name: "Salads", icon: "🥗" },
    { id: 6, name: "Breakfast", icon: "🥞" }
  ],
  products: [
    {
      id: 101,
      categoryId: 1,
      name: "Classic Cheeseburger",
      price: 5.99,
      image: "https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=400&h=300&fit=crop",
      description: "Juicy beef patty with melted cheese, lettuce, tomato, and our special sauce",
      modifierGroups: [
        {
          id: "size",
          name: "Size",
          type: "single",
          required: true,
          options: [
            { id: "reg", label: "Regular", delta: 0 },
            { id: "lg", label: "Large", delta: 1.50 }
          ]
        },
        {
          id: "extras",
          name: "Add-ons",
          type: "multiple",
          required: false,
          min: 0,
          max: 3,
          options: [
            { id: "bacon", label: "Bacon", delta: 1.00 },
            { id: "avocado", label: "Avocado", delta: 0.75 },
            { id: "mushrooms", label: "Mushrooms", delta: 0.50 }
          ]
        }
      ]
    },
    {
      id: 102,
      categoryId: 1,
      name: "BBQ Bacon Burger",
      price: 7.99,
      image: "https://images.unsplash.com/photo-1571091718767-18b5b1457add?w=400&h=300&fit=crop",
      description: "Smoky BBQ sauce, crispy bacon, cheddar cheese, and onion rings",
      modifierGroups: [
        {
          id: "size",
          name: "Size",
          type: "single",
          required: true,
          options: [
            { id: "reg", label: "Regular", delta: 0 },
            { id: "lg", label: "Large", delta: 1.50 }
          ]
        }
      ]
    },
    {
      id: 103,
      categoryId: 1,
      name: "Veggie Delight",
      price: 6.99,
      image: "https://images.unsplash.com/photo-1525059693554-2d1d7bcf7c30?w=400&h=300&fit=crop",
      description: "Plant-based patty with fresh vegetables and vegan cheese",
      modifierGroups: [
        {
          id: "size",
          name: "Size",
          type: "single",
          required: true,
          options: [
            { id: "reg", label: "Regular", delta: 0 },
            { id: "lg", label: "Large", delta: 1.50 }
          ]
        }
      ]
    },
    {
      id: 201,
      categoryId: 2,
      name: "French Fries",
      price: 3.99,
      image: "https://images.unsplash.com/photo-1576107232684-1279f390859f?w=400&h=300&fit=crop",
      description: "Golden crispy fries with sea salt",
      modifierGroups: [
        {
          id: "size",
          name: "Size",
          type: "single",
          required: true,
          options: [
            { id: "sm", label: "Small", delta: 0 },
            { id: "md", label: "Medium", delta: 1.00 },
            { id: "lg", label: "Large", delta: 2.00 }
          ]
        },
        {
          id: "seasoning",
          name: "Seasoning",
          type: "single",
          required: false,
          options: [
            { id: "plain", label: "Plain", delta: 0 },
            { id: "cajun", label: "Cajun", delta: 0.50 },
            { id: "garlic", label: "Garlic", delta: 0.50 }
          ]
        }
      ]
    },
    {
      id: 202,
      categoryId: 2,
      name: "Onion Rings",
      price: 4.99,
      image: "https://images.unsplash.com/photo-1572441713132-51c75654db73?w=400&h=300&fit=crop",
      description: "Crispy beer-battered onion rings",
      modifierGroups: [
        {
          id: "size",
          name: "Size",
          type: "single",
          required: true,
          options: [
            { id: "reg", label: "Regular (6 pcs)", delta: 0 },
            { id: "lg", label: "Large (10 pcs)", delta: 2.00 }
          ]
        }
      ]
    },
    {
      id: 301,
      categoryId: 3,
      name: "Coca-Cola",
      price: 2.99,
      image: "https://images.unsplash.com/photo-1581636625402-29b2a704ef13?w=400&h=300&fit=crop",
      description: "Classic Coca-Cola",
      modifierGroups: [
        {
          id: "size",
          name: "Size",
          type: "single",
          required: true,
          options: [
            { id: "sm", label: "Small (16oz)", delta: 0 },
            { id: "md", label: "Medium (20oz)", delta: 0.50 },
            { id: "lg", label: "Large (32oz)", delta: 1.00 }
          ]
        }
      ]
    },
    {
      id: 302,
      categoryId: 3,
      name: "Fresh Orange Juice",
      price: 3.99,
      image: "https://images.unsplash.com/photo-1621506289937-a8e4df240d0b?w=400&h=300&fit=crop",
      description: "Freshly squeezed orange juice",
      modifierGroups: [
        {
          id: "size",
          name: "Size",
          type: "single",
          required: true,
          options: [
            { id: "sm", label: "Small (12oz)", delta: 0 },
            { id: "md", label: "Medium (16oz)", delta: 1.00 },
            { id: "lg", label: "Large (20oz)", delta: 2.00 }
          ]
        }
      ]
    },
    {
      id: 401,
      categoryId: 4,
      name: "Chocolate Cake",
      price: 4.99,
      image: "https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=400&h=300&fit=crop",
      description: "Rich chocolate cake with chocolate frosting",
      modifierGroups: []
    },
    {
      id: 402,
      categoryId: 4,
      name: "Ice Cream Sundae",
      price: 3.99,
      image: "https://images.unsplash.com/photo-1563805042-7684c019e1cb?w=400&h=300&fit=crop",
      description: "Vanilla ice cream with your choice of toppings",
      modifierGroups: [
        {
          id: "toppings",
          name: "Toppings",
          type: "multiple",
          required: false,
          min: 0,
          max: 4,
          options: [
            { id: "chocolate", label: "Chocolate Syrup", delta: 0.50 },
            { id: "strawberry", label: "Strawberry Syrup", delta: 0.50 },
            { id: "caramel", label: "Caramel", delta: 0.50 },
            { id: "nuts", label: "Chopped Nuts", delta: 0.75 },
            { id: "cherry", label: "Cherry", delta: 0.25 }
          ]
        }
      ]
    },
    {
      id: 501,
      categoryId: 5,
      name: "Caesar Salad",
      price: 8.99,
      image: "https://images.unsplash.com/photo-1546793665-c74683f339c1?w=400&h=300&fit=crop",
      description: "Fresh romaine lettuce with Caesar dressing, croutons, and parmesan",
      modifierGroups: [
        {
          id: "protein",
          name: "Add Protein",
          type: "single",
          required: false,
          options: [
            { id: "none", label: "No Protein", delta: 0 },
            { id: "chicken", label: "Grilled Chicken", delta: 3.00 },
            { id: "salmon", label: "Grilled Salmon", delta: 4.00 }
          ]
        }
      ]
    },
    {
      id: 601,
      categoryId: 6,
      name: "Pancakes",
      price: 6.99,
      image: "https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=400&h=300&fit=crop",
      description: "Fluffy pancakes served with syrup and butter",
      modifierGroups: [
        {
          id: "toppings",
          name: "Toppings",
          type: "multiple",
          required: false,
          min: 0,
          max: 3,
          options: [
            { id: "berries", label: "Mixed Berries", delta: 1.50 },
            { id: "banana", label: "Sliced Banana", delta: 0.75 },
            { id: "chocolate", label: "Chocolate Chips", delta: 1.00 },
            { id: "nuts", label: "Walnuts", delta: 1.25 }
          ]
        }
      ]
    }
  ]
};

export const users = [
  { id: 1, email: "admin@kiosk.com", password: "admin123", name: "Admin" },
  { id: 2, email: "test@kiosk.com", password: "test123", name: "Test User" }
];
