FROM node:20

# Set the working directory
WORKDIR /app

# Copy the app files
COPY . .

# Install dependencies
RUN npm install

# Expose port 3000
EXPOSE 3000

# Command to start the application
CMD ["npm", "run", "build"]
